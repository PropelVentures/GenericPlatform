<?php

require_once 'functions_loader.php';


	log_event('logout','logout');
	@session_start();

	@session_destroy();

	echo "<script>window.location='../index.php';</script>";
