-- Inserting admin user
INSERT INTO users (username, password, full_name, email, is_admin) VALUES ('admin', MD5('adminpassword'), 'Admin User', 'admin@example.com', TRUE);

-- Inserting sample courses
INSERT INTO courses (title, description, instructor_id) VALUES ('Accounting Basics', 'Learn the basics of accounting', 1);
INSERT INTO courses (title, description, instructor_id) VALUES ('Advanced Accounting', 'Learn advanced accounting topics', 1);

-- Inserting sample content
INSERT INTO content (course_id, title, content_type, content_path) VALUES (1, 'Introduction to Accounting', 'video', 'path/to/video1.mp4');
INSERT INTO content (course_id, title, content_type, content_path) VALUES (1, 'Accounting Notes', 'notes', 'path/to/notes1.pdf');
INSERT INTO content (course_id, title, content_type, content_path) VALUES (2, 'Advanced Accounting Video', 'video', 'path/to/video2.mp4');
INSERT INTO content (course_id, title, content_type, content_path) VALUES (2, 'Advanced Accounting Notes', 'notes', 'path/to/notes2.pdf');
