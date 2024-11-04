<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\lists\Playlist;
use PDO;
use PDOException;

class DeefyRepository
{
    private PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    
    private function __construct(array $conf) {
        $this->pdo = new PDO($conf['dsn'], $conf['user'], $conf['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,]);
        
    }
    
    //GETTER
    
    public static function getInstance(): DeefyRepository {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    public function getPdo()
    {
        return $this->pdo;
    }


    public static function setConfig(string $file): void {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Error reading configuration file");
        }
        self::$config = [
            'dsn' => "mysql:host=" . $conf['host'] . ";dbname=" . $conf['dbname'],
            'user' => $conf['username'],
            'pass' => $conf['password']
        ];
    }


    public function getAllPlaylists(): array {
        $stmt = $this->pdo->query("SELECT * FROM playlist");
        $playlistsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $playlists = [];
        foreach ($playlistsData as $playlistData) {
            $playlists[] = new Playlist($playlistData['nom']);
        }
    
        return $playlists;
    }

    public function saveTrack(AudioTrack $track): int
    {
        if ($track instanceof AlbumTrack) {
            $stmt = $this->pdo->prepare('INSERT INTO track (titre, filename, artiste_album, numero_album, duree) 
                                         VALUES (:titre, :filename, :artiste_album, :numero_album, :duree)');
            $stmt->execute([
                'titre' => $track->titre,
                'filename' => $track->nom_du_fichier,
                'artiste_album' => $track->artiste,
                'numero_album' => $track->numero_piste,
                'duree' => $track->duree
            ]);
        } else {
            throw new PDOException(":");
        }
        return $this->pdo->lastInsertId();
    }

    public function saveEmptyPlaylist(Playlist $playlist): Playlist
    {
        $stmt = $this->pdo->prepare('INSERT INTO playlist (nom) VALUES (:nom)');
        $stmt->execute(['nom' => $playlist->nom]);
        $playlistId = $this->pdo->lastInsertId();
        $_SESSION['playlist_id'] = $playlistId;

        $stmt = $this->pdo->prepare('INSERT INTO user2playlist (id_user, id_pl) VALUES (:id_user, :id_pl)');
        $stmt->execute(['id_user' => unserialize($_SESSION['user'])->id, 'id_pl' => $playlistId]);

        return $playlist;
    }

    public function getTrackNumber(int $id_pl): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM playlist2track WHERE id_pl = :id_pl');
        $stmt->execute(['id_pl' => $id_pl]);
        return $stmt->fetchColumn();
    }


    public function addTrackToPlaylist(int $id_track, int $id_pl): void
    {
        
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM track WHERE id = :id_track');
        $stmt->execute(['id_track' => $id_track]);
        if ($stmt->fetchColumn() == 0) {
            throw new PDOException("Identifiant piste inexistant");
        }

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM playlist WHERE id = :id_pl');
        $stmt->execute(['id_pl' => $id_pl]);
        if ($stmt->fetchColumn() == 0) {
            throw new PDOException("Identifiant playlist inexistant");
        }

        $stmt = $this->pdo->prepare('INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) VALUES (:id_pl, :id_track, :no_piste_dans_liste)');
        $stmt->execute(['id_pl' => $id_pl, 'id_track' => $id_track, 'no_piste_dans_liste' => $this->getTrackNumber($id_pl) + 1]);
    }


    public function findPlaylistById(int $id): ?Playlist
{
    // Récupère les informations de la playlist
    $stmt = $this->pdo->prepare('SELECT * FROM playlist WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $playlistData = $stmt->fetch();

    if (!$playlistData){
        throw new PDOException("Playlist Introuvable");
    };

    $playlist = new Playlist($playlistData['nom']);

    // Récupère toutes les pistes associées à la playlist
    $stmt = $this->pdo->prepare(
        'SELECT t.* FROM track t 
         JOIN playlist2track p2t ON t.id = p2t.id_track 
         WHERE p2t.id_pl = :id'
    );
    $stmt->execute(['id' => $id]);
    $tracksData = $stmt->fetchAll();

    // Ajoute chaque piste à la playlist en tant qu'AlbumTrack
    foreach ($tracksData as $trackData) {
        $track = new AlbumTrack(
            $trackData['titre'],
            $trackData['filename'],
            $trackData['artiste_album'] ?? 'Artiste inconnu',
            $trackData['numero_album'] ?? 0,
            $trackData['duree']
        );
        $playlist->ajouterPiste($track);
    }

    return $playlist;
}

    

    
}
