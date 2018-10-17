<h3>GENERATE DATA DICTIONARY<h3>

        <h3><a  href='?action=newDD' onclick="return confirm('Are you sure ,you want to Generate NEW DD')"> CLICK HERE TO GENERATE New DD DEFINITION</a></h3>

        <h3><a  href='?action=appendDD' onclick="return confirm('Are you sure ,you want to UPDATE DD')"> CLICK HERE TO UPDATE/Append  DD DEFINITION</a></h3>


        <?php

        /**
         * here for now ,generic DD init routine wont work ( which we might not need either )
         */
        function newDD() {

            require('dictionaryConfig.php');

            //connecting with generic database

            $con = connect($config);

            $con_generic = connect_generic();

            $con->query("CREATE TABLE IF NOT EXISTS `data_dictionary` (
  `dict_id` int(10) NOT NULL AUTO_INCREMENT,
  `table_alias` varchar(50) DEFAULT NULL,
  `database_table_name` varchar(35) DEFAULT NULL,
  `table_type` varchar(20) DEFAULT NULL,
  `parent_table` varchar(50) DEFAULT NULL,
  `keyfield` varchar(50) DEFAULT NULL,
  `display_page` varchar(50) DEFAULT NULL,
  `tab_num` varchar(8) DEFAULT NULL,
  `tab_name` varchar(50) DEFAULT NULL,
  `dd_editable` int(1) NOT NULL DEFAULT '1',
  `dd_visibility` varchar(20) DEFAULT NULL,
  `user_type` varchar(50) DEFAULT NULL,
  `dd_privilege_level` varchar(20) DEFAULT NULL,
  `list_views` varchar(50) DEFAULT NULL,
  `list_filter` varchar(100) DEFAULT NULL,
  `list_sort` varchar(100) DEFAULT NULL,
  `list_extra_options` text NOT NULL,
  `list_style` varchar(100) NOT NULL,
  `description` text,
  `created` datetime DEFAULT NULL,
  `list_fields` varchar(100) DEFAULT NULL,
  `fields_used` varchar(30) NOT NULL,
  `fd_initialization` varchar(100) NOT NULL,
  `list_select` varchar(200) NOT NULL,
  PRIMARY KEY (`dict_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

            $rs = $con->query("SHOW COLUMNS FROM $DDtbl");



            $ddKeys = array();



            while ($DDCol = $rs->fetch_assoc()) {

                $ddKeys[] = $DDCol['Field'];
            }


            /*
             * 
             * emptying the DD table
             */

            // $con = connect();


            $con->query("truncate table $DDtbl");


            $rs = $con_generic->query("select * from $DDT");


            while ($row = $rs->fetch_array(MYSQLI_ASSOC)) {

                //print_r($row);die;
                unset($row['dict_id']);

                //$ddKeys = array('database_table_name', 'table_alias', 'table_type', 'tab_name', 'keyfield', 'parent_table', 'list_filter', 'list_sort');
/// searching/replacing constant 
                if (isset($APP_DEFAULT['DD']) && !empty($APP_DEFAULT['DD'])) {

                    foreach ($ddKeys as $k) {
//echo $k;
                        ///to skip search/replace of field table_alias & table_type
                        if ($k != 'table_alias' || $k != 'table_type') {
                            
                        
                            if (isset($row[$k])) {

                                $dd_key = (array_keys($DEFAULT['DD']));

                                foreach ($dd_key as $key) {

                                    $row[$k] = str_replace($key, $DEFAULT['DD'][$key], $row[$k]);
                                }///inner foreach
                            }
                        }
                    }///outer foreach
                }
//echo "<pre>";
//print_r($row);die;

                insert($DDtbl, $row, $config);
            }

            echo "<pre> Data Dictionary Generated Successfully";
            $con->close();
        }

///end of newDD generation

        function appendDD() {

            require('dictionaryConfig.php');


            $con = connect($config);

            $con_generic = connect_generic();

            $rs = $con->query("SHOW COLUMNS FROM $DDtbl");

            $ddKeys = array();



            while ($DDCol = $rs->fetch_assoc()) {

                $ddKeys[] = $DDCol['Field'];
            }



            $rs = $con->query("select * from $DDtbl");

            while ($row = $rs->fetch_array(MYSQLI_ASSOC)) {



                $id = $row['dict_id'];

                unset($row['dict_id']);

                // $ddKeys = array('database_table_name', 'table_alias', 'table_type', 'tab_name', 'keyfield', 'parent_table', 'list_filter', 'list_sort');

                if (isset($DEFAULT['DD']) && !empty($DEFAULT['DD'])) {

                    foreach ($ddKeys as $k) {

                        ///to skip search/replace of field table_alias & table_type
                        if ($k == 'table_alias' || $k == 'table_type') {
                            
                        } else {

                            if (isset($row[$k])) {

                                $dd_key = (array_keys($DEFAULT['DD']));

                                foreach ($dd_key as $key) {

                                    $row[$k] = str_replace($key, $DEFAULT['DD'][$key], $row[$k]);
                                }
                            }///inner foreach
                        }
                    }///outer foreach
                }

                update($DDtbl, $row, array('dict_id' => $id), $config);
            }

            echo "<pre> Data Dictionary Updated Successfully";
            $con->close();
        }

        if (isset($_GET['action'])) {

            $_GET['action']();
        } 