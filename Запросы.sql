
CREATE TABLE posts (
    id INT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL
);

CREATE TABLE comments (
    id INT PRIMARY KEY,
    post_id INT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(id)
);