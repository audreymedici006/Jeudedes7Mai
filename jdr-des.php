<?php
	/*
	Plugin Name: Générateur de lancés de dés
	Description: Extension pour la formation Dev Web
	Version:     1.0.0
	Author:      Audrey Medici
	*/
	// don't load directly
	if ( ! defined( 'ABSPATH' ) ) {
        die( '-1' );
	}
	// Plugin constantes
	define( 'JDR_DES_URL', plugin_dir_url( __FILE__ ) );
	require "JDR_DES_WIDGET.class.php";
	
	/* Appel hook des widgets */
	add_action( 'widgets_init', 'JDR_DES_WIDGET::register_widget' );
	
	/* Gestion activation / désactivation du plugin */
	register_activation_hook(__FILE__, 'JDR_DES_WIDGET::activation');
	
	/* On supprime les datas, mais cela devrait être sur le uninstall, mais le uninstall supprime le module pour tester c'est mieux ici */
	register_deactivation_hook(__FILE__, 'JDR_DES_WIDGET::deactivation');
	
	/* On supprime les datas */
	register_uninstall_hook( __FILE__, 'JDR_DES_WIDGET::deactivation' );
	
	/* Gestion des shortcodes */
	/* Permet d'ajouter dans une page [jdr-tirages] */
	JDR_DES_WIDGET::addShortcodes();
	
	
	
	/* Ajout pour appel ajax utilisateurs / administrateur */
	add_action( 'wp_ajax_nopriv_jdr_des_lances',array( 'JDR_DES_WIDGET','ajax_jdr_des_lances') );
	add_action( 'wp_ajax_jdr_des_lances',array( 'JDR_DES_WIDGET','ajax_jdr_des_lances') );
	
	/* Si on est dans la partie admin on ajoute le menu */
	if (is_admin()){
	/* Ajout pour appel menu admin */
	add_action( 'admin_menu', 'JDR_DES_WIDGET::AjoutMenu' );
	
	}
	

	