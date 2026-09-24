CREATE DATABASE IF NOT EXISTS jetpur_karate;
USE jetpur_karate;

CREATE TABLE students (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_code VARCHAR(30) NOT NULL UNIQUE,
  first_name VARCHAR(80) NOT NULL,
  last_name VARCHAR(80) NULL,
  gender ENUM('Male','Female','Other') NULL,
  date_of_birth DATE NULL,
  mobile VARCHAR(20) NULL,
  email VARCHAR(150) NULL,
  address TEXT NULL,
  admission_date DATE NOT NULL,
  batch_id INT UNSIGNED NULL,
  belt VARCHAR(50) NOT NULL DEFAULT 'White Belt',
  emergency_contact_name VARCHAR(150) NULL,
  emergency_contact_mobile VARCHAR(20) NULL,
  blood_group VARCHAR(10) NULL,
  photo VARCHAR(255) NULL,
  status ENUM('Active','Inactive','Pending') NOT NULL DEFAULT 'Active',
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_students_name (last_name, first_name),
  INDEX idx_students_mobile (mobile),
  INDEX idx_students_status (status),
  INDEX idx_students_batch (batch_id)
);

CREATE TABLE batches (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  timing VARCHAR(100) NULL,
  coach_name VARCHAR(150) NULL,
  status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE students
  ADD CONSTRAINT fk_students_batch
  FOREIGN KEY (batch_id) REFERENCES batches(id)
  ON DELETE SET NULL ON UPDATE CASCADE;

INSERT INTO batches (name,timing,coach_name) VALUES
('Morning A','06:00 AM - 07:00 AM','Head Coach'),
('Evening A','05:00 PM - 06:00 PM','Coach 1'),
('Morning B','07:00 AM - 08:00 AM','Coach 2'),
('Evening B','06:00 PM - 07:00 PM','Coach 3');

INSERT INTO students
(student_code,first_name,last_name,gender,date_of_birth,mobile,admission_date,batch_id,belt,status)
VALUES
('ST-1001','Aarav','Patel','Male','2015-04-12','9876543210','2026-06-01',1,'White Belt','Active'),
('ST-1002','Riya','Shah','Female','2014-09-21','9876543211','2026-05-18',2,'Yellow Belt','Active');

-- READ
SELECT s.*, b.name AS batch_name
FROM students s
LEFT JOIN batches b ON b.id=s.batch_id
ORDER BY s.id DESC;

-- CREATE
INSERT INTO students
(student_code,first_name,last_name,gender,date_of_birth,mobile,email,address,admission_date,batch_id,belt,emergency_contact_name,emergency_contact_mobile,status,notes)
VALUES
(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);

-- UPDATE
UPDATE students SET
first_name=?, last_name=?, gender=?, date_of_birth=?, mobile=?, email=?,
address=?, admission_date=?, batch_id=?, belt=?, emergency_contact_name=?,
emergency_contact_mobile=?, status=?, notes=?
WHERE id=?;

-- DELETE
DELETE FROM students WHERE id=?;
