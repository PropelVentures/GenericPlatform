<h3>GENERATE NAVIGATION<h3>

        <h3><a  href='?action=newDD' onclick="return confirm('Are you sure ,you want to Generate NEW NAVIGATION')"> CLICK HERE TO GENERATE New NAVIGATION</a></h3>

        <h3><a  href='?action=appendDD' onclick="return confirm('Are you sure ,you want to UPDATE NAVIGATION')"> CLICK HERE TO UPDATE/Append  NAVIGATION</a></h3>


        <?php

        /**
         * here for now ,generic DD init routine wont work ( which we might not need either )
         */
        function newDD() {

            require('dictionaryConfig.php');

            //connecting with generic database

            $con = connect($config);

            $con_generic = connect_generic();


            $con->query("CREATE TABLE IF NOT EXISTS `navigation` (
  `nav_id` int(11) NOT NULL AUTO_INCREMENT,
  `display_page` varchar(100) NOT NULL,
  `menu_location` varchar(100) NOT NULL DEFAULT 'header',
  `item_number` varchar(4) DEFAULT NULL,
  `item_label` varchar(100) NOT NULL,
  `item_target` varchar(100) NOT NULL,
  `target_display_page` varchar(100) NOT NULL,
  `loginRequired` enum('true','false') NOT NULL DEFAULT 'true',
  `item_help` varchar(100) NOT NULL,
  `item_style` varchar(100) NOT NULL,
  `item_privilege` varchar(100) NOT NULL,
  `item_icon` varchar(100) NOT NULL,
  `item_visibility` int(1) NOT NULL DEFAULT '1',
  `admin_level` int(1) DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `menu_orientation` varchar(50) NOT NULL DEFAULT 'HORIZONTAL',
  `page_layout_style` varchar(100) NOT NULL,
  PRIMARY KEY (`nav_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");


            $rs = $con->query("SHOW COLUMNS FROM $NAVtbl");



            $ddKeys = array();



            while ($DDCol = $rs->fetch_assoc()) {

                $ddKeys[] = $DDCol['Field'];
            }

            //print_r($ddKeys);die;

            /*
             * 
             * emptying the DD table
             */

            // $con = connect();


            $con->query("truncate table $NAVtbl");


            $rs = $con_generic->query("select * from $NAVT");


            while ($row = $rs->fetch_array(MYSQLI_ASSOC)) {

                //print_r($row);die;
                unset($row['nav_id']);

                //$ddKeys = array('database_table_name', 'table_alias', 'table_type', 'tab_name', 'keyfield', 'parent_table', 'list_filter', 'list_sort');
/// searching/replacing constant 
                if (isset($APP_DEFAULT) && !empty($APP_DEFAULT['DD'])) {


                    foreach ($ddKeys as $k) {

                        if (isset($row[$k])) {

                            $dd_key = (array_keys($DEFAULT['DD']));

                            foreach ($dd_key as $key) {

                                $row[$k] = str_replace($key, $DEFAULT['DD'][$key], $row[$k]);
                            }
                        }///inner foreach
                    }///outer foreach
                }
//echo "<pre>";
//print_r($row);die;

                insert($NAVtbl, $row, $config);
            }

            echo "<pre> Navigation Generated Successfully";
            $con->close();
        }

///end of newDD generation

        function appendDD() {

            require('dictionaryConfig.php');


            $con = connect($config);

            $con_generic = connect_generic();

            $rs = $con->query("SHOW COLUMNS FROM $NAVtbl");

            $ddKeys = array();



            while ($DDCol = $rs->fetch_assoc()) {

                $ddKeys[] = $DDCol['Field'];
            }



            $rs = $con->query("select * from $NAVtbl");

            while ($row = $rs->fetch_array(MYSQLI_ASSOC)) {



                $id = $row['nav_id'];

                unset($row['nav_id']);

                // $ddKeys = array('database_table_name', 'table_alias', 'table_type', 'tab_name', 'keyfield', 'parent_table', 'list_filter', 'list_sort');

                if (isset($APP_DEFAULT['DD']) && !empty($APP_DEFAULT['DD'])) {

                    foreach ($ddKeys as $k) {

                        if (isset($row[$k])) {

                            $dd_key = (array_keys($DEFAULT['DD']));

                            foreach ($dd_key as $key) {

                                $row[$k] = str_replace($key, $DEFAULT['DD'][$key], $row[$k]);
                            }
                        }///inner foreach
                    }///outer foreach
                }

                update($NAVtbl, $row, array('nav_id' => $id), $config);
            }

            echo "<pre> Navigation Updated Successfully";
            $con->close();
        }

        if (isset($_GET['action'])) {

            $_GET['action']();
        } 