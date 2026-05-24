START TRANSACTION;

ALTER TABLE `blog_comments`
    ADD COLUMN `social_comment_id` BIGINT UNSIGNED NULL AFTER `parent_id`,
    ADD UNIQUE KEY `blog_comments_social_comment_id_unique` (`social_comment_id`),
    ADD CONSTRAINT `blog_comments_social_comment_id_foreign`
        FOREIGN KEY (`social_comment_id`) REFERENCES `sosial_comments`(`id`)
        ON DELETE SET NULL;

ALTER TABLE `sosial_posts`
    ADD COLUMN `blog_id` BIGINT UNSIGNED NULL AFTER `user_id`,
    ADD UNIQUE KEY `sosial_posts_blog_id_unique` (`blog_id`),
    ADD CONSTRAINT `sosial_posts_blog_id_foreign`
        FOREIGN KEY (`blog_id`) REFERENCES `blogs`(`id`)
        ON DELETE CASCADE;

ALTER TABLE `sosial_comments`
    MODIFY `user_id` BIGINT UNSIGNED NULL,
    ADD COLUMN `author_name` VARCHAR(80) NULL AFTER `parent_id`;

COMMIT;

----------------------------Geri alma işlemi----------------------------
START TRANSACTION;

ALTER TABLE `blog_comments`
    DROP FOREIGN KEY `blog_comments_social_comment_id_foreign`,
    DROP INDEX `blog_comments_social_comment_id_unique`,
    DROP COLUMN `social_comment_id`;

ALTER TABLE `sosial_posts`
    DROP FOREIGN KEY `sosial_posts_blog_id_foreign`,
    DROP INDEX `sosial_posts_blog_id_unique`,
    DROP COLUMN `blog_id`;

ALTER TABLE `sosial_comments`
    DROP COLUMN `author_name`,
    MODIFY `user_id` BIGINT UNSIGNED NOT NULL;

COMMIT;
