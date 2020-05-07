<?php
	// The widget class
	class JDR_DES_WIDGET extends WP_Widget {
		/* Nom de la table de stockage des tirages */
		const TBL_TIRAGES = 'tirages_des';
		function __construct() {
			parent::__construct(
			// widget ID
			'jdr_des',
			// widget name
			__('Lancé de dés', ' jdr_des'),
			// widget description
			array( 'description' => __( 'Générateur de lancé de dés', 'jdr_des' ), )
			);
		}


		/* Gestion de l'aafichage sur le site dans la section dans laquelle le widget a été positionné*/
		public function widget( $args, $instance ) {
			
			/* Si utilisateur alors on a un ID correspondant à l'id de la table user de wordpress */
			/* Donc si pas d'id pas d'utilisateur connecté */
			/* pas d'utilisateur connecté, pas de widget */
			if (get_current_user_id()<1){
				return "";
			}
			
			/* Chargement du javascript qui gére l'appel ajax 
				Utilisation de la constantge JDR_DES_URL qui contient l'url de base du plugin
				Nous allons utiliser jquery, il faut qu'il soit chargé avant notre .js
			*/
			wp_enqueue_script( 'jdr-des-front',  JDR_DES_URL.'/js/front.js', array( 'jquery' ), '2.0.1' );
			/* Définition des variables passées au js 
				wp_create_nonce => protection des appels
			*/ 
			wp_localize_script( 'jdr-des-front', 'ajax_object', array( 
			'ajax_nonce' => wp_create_nonce('jdr-des-lances'),
			'ajax_url' => admin_url( 'admin-ajax.php'))
			);
			/* Ajout du css pour la mise en page */
			 wp_enqueue_style( 'jdr-des', JDR_DES_URL.'/css/widget.css' );
			

			$title = apply_filters( 'widget_title', $instance['title'] );
			echo $args['before_widget'];
			//Si on a un titre indiqué par la saisie dans le formulaire de la page apparence / widget on affiche ce titre
			if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
			//output
			
			
			/* un formulaire contenant la liste des éléments pour le tirage 
			   utilisation d'un champ input avec syntaxe tableau
			   data-de-face[nb faces du dés][nb] contient le nombre de tirages voulu
			   data-de-face[nb faces du dés][plus] contient la valuer à ajouter à chaque tirage.
			   Avantage facilité pour ajouter / modifier un tirage
			   On pourrait générer cette liste de dés via une boucle.
			   
			*/
			
			echo __( 'Générateur de lancés', 'jdr_des' );
			echo '<form class="frm_jdr_des" id="frm_'.$args['widget_id'].'">';
			echo 'd2 : <img class="jdr-des-img" src="'.JDR_DES_URL.'/images/de-2-faces.png" alt="2 faces"> : <input class="jdr_des_nb " name="data-de-face[2][nb]"  type="number" size="3" min="0">';
			echo '+ : <input class="jdr_des_plus" name="data-de-face[2][plus]"  type="number" size="3" min="0" ><br>';
			echo 'd4 : <img class="jdr-des-img" src="'.JDR_DES_URL.'/images/de-4-faces.png" alt="4 faces"> : <input class="jdr_des_nb" name="data-de-face[4][nb]"  type="number" size="3" min="0">';
			echo '+ :<input class="jdr_des_plus" name="data-de-face[4][plus]"  type="number" size="3" min="0"><br>';
			echo 'd6 : <img class="jdr-des-img" src="'.JDR_DES_URL.'/images/1-6-faces.png" alt="6 faces"> : <input class="jdr_des_nb" name="data-de-face[6][nb]"  type="number" size="3" min="0">';
			echo '+ :<input class="jdr_des_plus" name="data-de-face[6][plus]"  type="number" size="3" min="0"><br>';
			echo 'd8 : <img class="jdr-des-img" src="'.JDR_DES_URL.'/images/de-8-faces.png" alt="8 faces"> : <input class="jdr_des_nb" name="data-de-face[8][nb]"  type="number" size="3" min="0">';
			echo '+ :<input class="jdr_des_plus" name="data-de-face[8][plus]"  type="number" size="3" min="0"><br>';
			echo 'd10 : <img class="jdr-des-img" src="'.JDR_DES_URL.'/images/de-10-faces.png" alt="10 faces"> : <input class="jdr_des_nb" name="data-de-face[10][nb]"  type="number" size="3" min="0">';
			echo '+ :<input class="jdr_des_plus" name="data-de-face[10][plus]"  type="number" size="3" min="0"><br>';
			echo 'd12 : <img class="jdr-des-img" src="'.JDR_DES_URL.'/images/de-12-faces.png" alt="12 faces"> : <input class="jdr_des_nb" name="data-de-face[12][nb]"  type="number" size="3" min="0">';
			echo '+ :<input class="jdr_des_plus" name="data-de-face[12][plus]"  type="number" size="3" min="0"><br>';
			echo 'd20 <img class="jdr-des-img" src="'.JDR_DES_URL.'/images/de-20-faces.png" alt="20 faces"> : <input class="jdr_des_nb" name="data-de-face[20][nb]"  type="number" size="3" min="0">';
			echo '+ :<input class="jdr_des_plus" name="data-de-face[20][plus]"  type="number" size="3" min="0"><br>';
			echo 'd100 <img class="jdr-des-img" src="'.JDR_DES_URL.'/images/de-100-faces.png" alt="100 faces"> : <input class="jdr_des_nb" name="data-de-face[100][nb]"  type="number" size="3" min="0">';
			echo '+ :<input class="jdr_des_plus" name="data-de-face[100][plus]"  type="number" size="3" min="0">';
			echo '<br> <input class="jdr_des_lance" type="submit" value="Lancer">';
			echo '</form>';
			echo '<div class="jdr_des_res"></div>';
			echo $args['after_widget'];
		}

		/* Gestion du formulaire de saisie dans apparence / widget  */
		public function form( $instance ) {
			if ( isset( $instance[ 'title' ] ) )
			$title = $instance[ 'title' ];
			else
			$title = __( 'Titre', 'jdr_des' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="jdr-widget" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
		}

		/* Enregistrement du titre dans la BDD */
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			return $instance;
		}

		/* Création de la table de stockage */
		static function activation(){
			global $wpdb;
			/* TBL_TIRAGES : tirages_des 
			 $wpdb->prefix wp_
			 => wp_tirages_des */
			$wp_table = $wpdb->prefix.self::TBL_TIRAGES;
			/* On ajoute la table ssi elle n'existe pas déjà */
			
			if($wpdb->get_var( "show tables like '$wp_table'" ) != $wp_table){
				$sql = "CREATE TABLE `". $wp_table . "` ( ";
				$sql .= "  `id`  int(11)   NOT NULL auto_increment, ";
				$sql .= "  `user_id`  int(11)   NOT NULL, ";
				$sql .= "  `tirage`  text   NOT NULL, ";
				$sql .= "  `date_tirage`  datetime   NOT NULL, ";
				$sql .= "  PRIMARY KEY `id` (`id`), KEY `user_id` (`user_id`) "; 
				$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ; ";
				/* Utilisation du gestionnaire de version de WP */
				require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
				dbDelta($sql);
			}
		}
		/* Destruction de la table de stockage */
		static function deactivation(){
			global $wpdb;
			$wp_table = $wpdb->prefix.self::TBL_TIRAGES;
			$sql = "DROP TABLE IF EXISTS $wp_table";
			$wpdb->query($sql);
		}
		
		
		/* Traitement appel Ajax depuis le widget cette methode est appeler par le javas cript quand o */
		public static function ajax_jdr_des_lances() {
			global $wpdb;
			
			if (get_current_user_id()<1){
				/* pas d'utilisateur connecté, pas de widget */
			die( 'No user'); 
			}
			/* Variables html tableau de ligne html pour affichage resultats tirages */
			$html=array();
			/* Total de la ligne du tirage */
			$total = 0;

			/* Vérification du token de sécurité 
				limite/évite que le déclenchement ajax soit fait sans l'utilisation de WP
				Cf DOC wordprence nonce ...
				*/
			if ( ! wp_verify_nonce( $_POST['nonce'], 'jdr-des-lances' ) ) {
				die( 'Security check'); 
			}

			/* 
				fonction php parse_str 
				permet de décoder les valeurs envoyées par le js 'data': $( this ).serialize(),
				data contient un tableau des champs input
				data-de-face[2][nb]
				data-de-face[2][plus]
				data-de-face[8][nb]
				data-de-face[8][plus]
			*/

			
			parse_str($_POST['data'], $arrDes);

			foreach ($arrDes['data-de-face'] as $face => $tirage){
				/* Pour éviter toute erreur on force le type des variables à entier */
				$nb_faces=(int) $face;
				$nb_tirages=(int) $tirage['nb'];
				$ajustement=(int) $tirage['plus'];
				/* Sécurité pour éviter de charger le serveur */
				if ($nb_tirages > 1000 || $nb_faces > 150 ){
					wp_die('Max');
				}
				/* Pas de calcul si l'un des deux est à zéro*/
				if (!$nb_faces || !$nb_tirages)
				continue;
				
				
				/* on appelle la methode statique total_tirage_de de l'objet JDR_DES_WIDGET représenté par "self" 
				JDR_DES_WIDGET::total_tirage_de
				*/
				$res_tirage=self::total_tirage_de($nb_faces,$nb_tirages,$ajustement);
				$total+=$res_tirage['total'];
				$html[]=$nb_tirages.'d'.$nb_faces.' ['.$res_tirage['details'].']';
			}
			/* Création de l'html qui sera affiché et stocké dans la BDD */
			$output['html']=join(' + ',$html) . " = " . $total;
			
			/* Stockage BDD */
			$table = $wpdb->prefix.self::TBL_TIRAGES;
			$data = array(
			'user_id' => get_current_user_id(), 
			'tirage' => $output['html'],
			'date_tirage'=> current_time('mysql', 1)
			);
			
			/* 3 params Entier / Chaine / Chaine */
			$format = array('%d','%s','%s');
			
			/* Ajout de l'enregistrement dans la base */
			$wpdb->insert($table,$data,$format);

			/* Pas utilisé, on peut ajouter un test sur la valeur et si 0 déclencher une erreur */
			$id = $wpdb->insert_id;
			

			/* Récupère le pseudo de l'utilisateur connecté */
			$user_info = get_userdata(get_current_user_id());
			$username = $user_info->user_login;
			$output['html']=$username . ' : ' . $output['html'];
			/* Sortie en format JSON*/
			print json_encode($output);
			/* Un appel Ajax doit se terminer par un wp_die() */
			wp_die();
		}
		
			/* 
			Effectue les nb_tirages aléatoires pour des dés de nb_faces en ajoutant un ajustement à chaque tirage 
			retourne un tableau contenant le détail des tirages et l'addition des valeurs obtenues
			nb_faces : représente le nombre de faces du dé
			nb_tirages : nombre de tirages a faire
			ajustement : le nombre de points à ajouter à chaque tirage de dé
			
			*/
			public static function total_tirage_de($nb_faces,$nb_tirages,$ajustement) {
			/* initiatisation de la variable $total */
			$total=0;
			/* Boucle des Tirages aléatoires */
			for ($i=0;$i<$nb_tirages;$i++){
				/*  valeur contient le resultat aléatoire entre 1 chiffre minimum et maxium nombre de faces du dé */
				/* Exemple d6 => rand(1,6) */
				$valeur = rand(1,$nb_faces);
				$valeur+=$ajustement;
				/* Si supérieur au max on retourne le max ) */
				if ($valeur > $nb_faces) {
					$valeur=$nb_faces;
				}
				$tirages[]=$valeur;
				$total+=$valeur;
			}
			// fin de boucle
			$res=array('details'=>join(',',$tirages), 'total'=>$total);
			return $res;
		}
		
		
		/* Affiche la listes des derniers 500 tirages pour l'utilisateur connecté */
		public static function liste_des_tirages($args=array()){
			global $wpdb;
			$table = $wpdb->prefix.self::TBL_TIRAGES;
			$query = "select * from $table where user_id=" . get_current_user_id() . " order by date_tirage desc limit 0,500";
			$results = $wpdb->get_results( $query, OBJECT  );
			$res= "<pre>";
			foreach ($results as $row){
				$user_info = get_userdata($row->user_id);
				$username = $user_info->user_login;
				$res.="$username [" . $row->date_tirage . "] : " . $row->tirage . "\n";
			}
			$res.= "</pre>";	
			return $res;
		}
		
		/* Ajout de l'ensemble des shortcodes */
		static function addShortcodes(){
			add_shortcode( 'jdr-tirages', 'JDR_DES_WIDGET::liste_des_tirages',true );
		}

	static function page_admin(){
		echo '<h1>Widget Lancés des dés</h1>';
		echo '<img src="'.JDR_DES_URL.'/images/grand-de.png">';
		echo '<p>Pour utiliser cette extension, vous devez ajouter le Widget dans apparence > Widgets</p>';
		echo '<p>Pour afficher les historiques des tirages vous pouvez utiliser le shortcode dans une page</p>';
		echo '<p><strong>[jdr-tirages]</strong></p>';
		echo '<br><em>Développé par Audrey</em>';
	}	
	static function AjoutMenu(){
		add_menu_page('Lancé de dés', 'Lancé de dés', 'manage_options', 'jdr-des','JDR_DES_WIDGET::page_admin',JDR_DES_URL.'/images/petit-de.png',2);
	}
		

	/* Ajoute la classe JDR_DES_WIDGET dans le moteur de gestion des Widgets de WP */
	public static function register_widget() {
		register_widget( 'JDR_DES_WIDGET' );
		}

}	// End Class					