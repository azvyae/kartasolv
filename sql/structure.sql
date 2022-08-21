CREATE TABLE communities (
    community_id int NOT NULL AUTO_INCREMENT,
    community_name varchar(128) NOT NULL,
    community_address varchar(256) NOT NULL,
    community_identifier varchar(256) DEFAULT NULL,
    pmpsks_type int NOT NULL,
    community_status enum('Disetujui', 'Belum Disetujui') NOT NULL DEFAULT 'Belum Disetujui',
    created_by int DEFAULT NULL,
    modified_by int DEFAULT NULL,
    deleted_by int DEFAULT NULL,
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    deleted_at datetime DEFAULT NULL,
    PRIMARY KEY (community_id),
    FOREIGN KEY (pmpsks_type) REFERENCES pmpsks_types (pmpsks_id),
    FOREIGN KEY (created_by) REFERENCES users (user_id),
    FOREIGN KEY (modified_by) REFERENCES users (user_id),
    FOREIGN KEY (deleted_by) REFERENCES users (user_id)
);

CREATE TABLE info_activities (
    id int NOT NULL DEFAULT '1',
    title_a varchar(64) NOT NULL DEFAULT '',
    desc_a varchar(512) NOT NULL DEFAULT '0',
    image_a varchar(256) NOT NULL DEFAULT '',
    title_b varchar(64) NOT NULL DEFAULT '',
    desc_b varchar(512) NOT NULL DEFAULT '',
    image_b varchar(256) NOT NULL DEFAULT '',
    title_c varchar(64) NOT NULL DEFAULT '',
    desc_c varchar(512) NOT NULL DEFAULT '',
    image_c varchar(256) NOT NULL DEFAULT '',
    modified_by int DEFAULT NULL,
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (modified_by) REFERENCES users (user_id)
);

CREATE TABLE info_history (
    id int NOT NULL DEFAULT '1',
    title_a varchar(64) NOT NULL DEFAULT '',
    desc_a varchar(512) NOT NULL DEFAULT '',
    title_b varchar(64) NOT NULL DEFAULT '',
    desc_b varchar(512) NOT NULL DEFAULT '',
    title_c varchar(64) NOT NULL DEFAULT '',
    desc_c varchar(512) NOT NULL DEFAULT '',
    title_d varchar(64) NOT NULL DEFAULT '',
    desc_d varchar(512) NOT NULL DEFAULT '',
    image_a varchar(256) NOT NULL DEFAULT '',
    image_b varchar(256) NOT NULL DEFAULT '',
    modified_by int DEFAULT '0',
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (modified_by) REFERENCES users (user_id)
);

CREATE TABLE info_landing (
    id int NOT NULL DEFAULT '1',
    landing_title varchar(64) NOT NULL DEFAULT '',
    landing_tagline varchar(512) NOT NULL DEFAULT '',
    cta_text varchar(16) NOT NULL DEFAULT '',
    cta_url varchar(256) NOT NULL DEFAULT '',
    vision varchar(512) NOT NULL DEFAULT '',
    landing_image varchar(256) NOT NULL DEFAULT '',
    mission mediumtext NOT NULL,
    modified_by int DEFAULT NULL,
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    deleted_at datetime DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (modified_by) REFERENCES users (user_id)
);

CREATE TABLE members (
    member_id int NOT NULL AUTO_INCREMENT,
    member_name varchar(50) NOT NULL DEFAULT '',
    member_position varchar(50) NOT NULL DEFAULT '',
    member_type enum('1', '2', '3', '4') NOT NULL DEFAULT '3',
    member_active enum('Aktif', 'Nonaktif') NOT NULL DEFAULT 'Aktif',
    member_image varchar(256) NOT NULL DEFAULT '',
    created_by int DEFAULT NULL,
    modified_by int DEFAULT NULL,
    deleted_by int DEFAULT NULL,
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    deleted_at datetime DEFAULT NULL,
    PRIMARY KEY (member_id),
    FOREIGN KEY (modified_by) REFERENCES users (user_id),
    FOREIGN KEY (created_by) REFERENCES users (user_id),
    FOREIGN KEY (deleted_by) REFERENCES users (user_id)
);

CREATE TABLE menu (
    menu_id int NOT NULL AUTO_INCREMENT,
    menu_string varchar(50) NOT NULL,
    PRIMARY KEY (menu_id)
);

CREATE TABLE messages (
    message_id int NOT NULL AUTO_INCREMENT,
    message_sender varchar(64) NOT NULL,
    message_whatsapp varchar(32) NOT NULL,
    message_type enum('Kritik & Saran', 'Laporan/Aduan') NOT NULL DEFAULT 'Kritik & Saran',
    message_text varchar(280) NOT NULL DEFAULT '',
    message_status enum('Dibaca', 'Belum Dibaca') NOT NULL DEFAULT 'Belum Dibaca',
    modified_by int DEFAULT NULL,
    deleted_by int DEFAULT NULL,
    created_at datetime DEFAULT NULL,
    updated_at datetime DEFAULT NULL,
    deleted_at datetime DEFAULT NULL,
    PRIMARY KEY (message_id),
    FOREIGN KEY (deleted_by) REFERENCES users (user_id),
    FOREIGN KEY (modified_by) REFERENCES users (user_id)
);

CREATE TABLE pages (
    page_id int NOT NULL AUTO_INCREMENT,
    page_title varchar(64) NOT NULL,
    page_url varchar(256) NOT NULL,
    page_icon varchar(128) NOT NULL,
    menu_id int NOT NULL,
    PRIMARY KEY (page_id),
    FOREIGN KEY (menu_id) REFERENCES menu (menu_id)
);

CREATE TABLE pmpsks_img (
    pmpsks_img_id int NOT NULL AUTO_INCREMENT,
    community_id int NOT NULL,
    pmpsks_img_loc varchar(256) NOT NULL,
    PRIMARY KEY (pmpsks_img_id),
    FOREIGN KEY (community_id) REFERENCES communities (community_id)
);

CREATE TABLE pmpsks_types (
    pmpsks_id int NOT NULL AUTO_INCREMENT,
    pmpsks_name varchar(128) NOT NULL,
    pmpsks_type enum('PMKS', 'PSKS') NOT NULL,
    pmpsks_category enum('Individu', 'Keluarga', 'Lembaga') NOT NULL DEFAULT 'Individu',
    deleted_at datetime DEFAULT NULL,
    PRIMARY KEY (pmpsks_id)
);

CREATE TABLE roles (
    role_id int NOT NULL AUTO_INCREMENT,
    role_string varchar(50) NOT NULL,
    role_name varchar(50) NOT NULL,
    PRIMARY KEY (role_id),
);

CREATE TABLE role_access (
    role_access_id int NOT NULL AUTO_INCREMENT,
    role_id int NOT NULL,
    menu_id int NOT NULL,
    PRIMARY KEY (role_access_id),
    FOREIGN KEY (menu_id) REFERENCES menu (menu_id),
    FOREIGN KEY (role_id) REFERENCES roles (role_id)
);

CREATE TABLE users (
    user_id int NOT NULL AUTO_INCREMENT,
    role_id int NOT NULL,
    user_name varchar(128) NOT NULL,
    user_email varchar(64) NOT NULL,
    user_password varchar(128) NOT NULL,
    user_temp_mail varchar(64) DEFAULT NULL,
    user_reset_attempt datetime DEFAULT NULL,
    user_change_mail datetime DEFAULT NULL,
    user_last_login datetime DEFAULT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT NULL,
    PRIMARY KEY (user_id),
    FOREIGN KEY (role_id) REFERENCES roles (role_id)
);