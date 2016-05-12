<?php

    namespace Aside\Includes;

    use \Aside\Settings as Settings;

    class Tables
    {
        /**
         * Tables
         * @var
         */
        private static $tables;

        /**
         * Init
         */
        public static function init()
        {
            // set tables
            foreach (array('images') as $v) {
                self::$tables[$v] = Settings::getTable($v);
            }
        }

        /**
         * Is
         */
        public static function is()
        {
            $result = DB::query(
                "SHOW TABLES LIKE '".self::$tables['images']."'"
            );

            return (bool)$result->num_rows;
        }

        /**
         * Create
         */
        public static function create()
        {
            // notes, notes comments
            DB::query(
                "CREATE TABLE IF NOT EXISTS `".self::$tables['images']."` (
  `id` int(6) unsigned NOT NULL,
  `name` varchar(250) NOT NULL,
  `size` varchar(14) NOT NULL,
  `width` varchar(14) NOT NULL,
  `height` varchar(14) NOT NULL,
  `extension` varchar(3) NOT NULL,
  `mime` varchar(20) NOT NULL,
  `color_1` varchar(6) NOT NULL,
  `color_2` varchar(6) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            DB::query("ALTER TABLE `".self::$tables['images']."` ADD PRIMARY KEY (`id`);");
            DB::query("ALTER TABLE `".self::$tables['images']."`
  MODIFY `id` int(6) unsigned NOT NULL AUTO_INCREMENT;");
        }
    }