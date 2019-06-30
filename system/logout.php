<?php

require_once 'functions_loader.php';
	log_event('logout','logout');
	// @session_start();
	start_app_session();
	@session_destroy();

	echo "<script>window.location='../index.php';</script>";
