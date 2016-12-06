
/*
 * verified_status: -1 = denied. 0 = pending. 1 = accepted.
 */

CREATE TABLE tech_users
(
    username VARCHAR(50) NOT NULL PRIMARY KEY,
    student_id CHAR(40) NOT NULL,
    verified_status TINYINT DEFAULT 0,
    verification_code CHAR(20),
    verification_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

/*
 * Alters table to store student id as a Sha256 encrypted string (11-11-16)
 */
ALTER TABLE tech_users CHANGE COLUMN student_id student_id CHAR(64) NOT NULL;
