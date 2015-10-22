<?php

namespace tdt4237\webapp;

use tdt4237\webapp\models\User;

class Sql
{
    static $pdo;
    private $userRepository;

    function __construct()
    {
    }

    /**
     * Create tables.
     */
    static function up()
    {
        $q1 = "CREATE TABLE users (id INTEGER PRIMARY KEY, user VARCHAR(50), pass VARCHAR(255), email varchar(50) default null, fullname varchar(50), address varchar(50), postcode varchar (4), age varchar(50), bio varhar(50), isadmin INTEGER);";
        $q6 = "CREATE TABLE posts (postId INTEGER PRIMARY KEY AUTOINCREMENT, author TEXT, title TEXT NOT NULL, content TEXT NOT NULL, date TEXT NOT NULL, isAnsweredByDoctor INTEGER, FOREIGN KEY(author) REFERENCES users(user));";
        $q7 = "CREATE TABLE comments(commentId INTEGER PRIMARY KEY AUTOINCREMENT, date TEXT NOT NULL, author TEXT NOT NULL, text INTEGER NOT NULL, belongs_to_post INTEGER NOT NULL, FOREIGN KEY(belongs_to_post) REFERENCES posts(postId));";
        $q8 = "CREATE TABLE payingusers(id INTEGER PRIMARY KEY, banr VARCHAR(16), ispaying INTEGER, totalpayed INTEGER);";
        $q9 = "CREATE TABLE doctors(id INTEGER PRIMARY KEY, totalearned INTEGER);";

        self::$pdo->exec($q1);
        self::$pdo->exec($q6);
        self::$pdo->exec($q7);
        self::$pdo->exec($q8);
        self::$pdo->exec($q9);

        print "[tdt4237] Done creating all SQL tables.".PHP_EOL;

        self::insertDummyUsers();
        self::insertPosts();
        self::insertComments();
        self::insertPayingUser();
        self::insertDummyDoctor();
    }

    static function insertDummyUsers()
    {
        $hash1 = Hash::createAPIHash(bin2hex(openssl_random_pseudo_bytes(2)));
        $hash2 = Hash::createAPIHash('bobdylan');
        $hash3 = Hash::createAPIHash('liverpool');
        $hash4 = Hash::createAPIHash('tardis');

        $q1 = "INSERT INTO users(user, pass, isadmin, fullname, address, postcode) VALUES ('admin', '$hash1', 1, 'admin', 'homebase', '9090')";
        $q2 = "INSERT INTO users(user, pass, isadmin, fullname, address, postcode) VALUES ('bob', '$hash2', 1, 'Robert Green', 'Greenland Grove 9', '2010')";
        $q3 = "INSERT INTO users(id, user, pass, isadmin, fullname, address, postcode) VALUES (99, 'bjarni', '$hash3', 1, 'Bjarni Torgmund', 'Hummerdale 12', '4120')";
        $q4 = "INSERT INTO users(id, user, pass, isadmin, fullname, address, postcode) VALUES (1001, 'drwho', '$hash4', 0, 'The Doctor', 'Gallifrey', '4242');";

        self::$pdo->exec($q1);
        self::$pdo->exec($q2);
        self::$pdo->exec($q3);
        self::$pdo->exec($q4);


        print "[tdt4237] Done inserting dummy users.".PHP_EOL;
    }

    static function insertPosts() {
        $q4 = "INSERT INTO posts(author, date, title, content, isAnsweredByDoctor) VALUES ('bob', '26082015', 'I have a problem', 'I have a generic problem I think its embarrasing to talk about. Someone help?', 0)";
        $q5 = "INSERT INTO posts(author, date, title, content, isAnsweredByDoctor) VALUES ('bjarni', '26082015', 'I also have a problem', 'I generally fear very much for my health', 1)";

        self::$pdo->exec($q4);
        self::$pdo->exec($q5);
        print "[tdt4237] Done inserting posts.".PHP_EOL;

    }

    static function insertComments() {
        $q1 = "INSERT INTO comments(author, date, text, belongs_to_post) VALUES ('bjarni', '26082015', 'Don''t be shy! No reason to be afraid here',0)";
        $q2 = "INSERT INTO comments(author, date, text, belongs_to_post) VALUES ('bob', '26082015', 'I wouldn''t worry too much, really. Just relax!',1)";
        self::$pdo->exec($q1);
        self::$pdo->exec($q2);
        print "[tdt4237] Done inserting comments.".PHP_EOL;

    }

    static function insertPayingUser()
    {
        $userid = 99;
        $banr = '1020304050607080';

        $q1 = "INSERT INTO payingusers (id, banr, ispaying, totalpayed) VALUES ('$userid', '$banr', 1, 0);";
        self::$pdo->exec($q1);
        print "[tdt4237] Done inserting paying user.".PHP_EOL;
    }

    static function insertDummyDoctor()
    {
        $userid = 1001;

        $q1 = "INSERT INTO doctors (id, totalearned) VALUES ('$userid', 0);";
        self::$pdo->exec($q1);
        print "[tdt4237] Done inserting dummy doctor.".PHP_EOL;
    }

    static function down()
    {
        $q1 = "DROP TABLE users";
        $q4 = "DROP TABLE posts";
        $q5 = "DROP TABLE comments";
        $q6 = "DROP TABLE payingusers";
        $q7 = "DROP TABLE doctors";



        self::$pdo->exec($q1);
        self::$pdo->exec($q4);
        self::$pdo->exec($q5);
        self::$pdo->exec($q6);
        self::$pdo->exec($q7);

        print "[tdt4237] Done deleting all SQL tables.".PHP_EOL;
    }
}
try {
    // Create (connect to) SQLite database in file
    Sql::$pdo = new \PDO('sqlite:app.db');
    // Set errormode to exceptions
    Sql::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    echo $e->getMessage();
    exit();
}
