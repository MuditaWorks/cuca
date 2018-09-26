<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa usar o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/pt-br:Editando_wp-config.php
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar estas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'cuca');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'root');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', '');

/** Nome do host do MySQL */
define('DB_HOST', 'localhost');

/** Charset do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8mb4');

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para invalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'mRNg<Ze.l -a2q-~17+a,*Nk[AbG9Nr=Pa]z!3vXJNj@xl6.C{[]7.Zhxq4vN8Ro');
define('SECURE_AUTH_KEY',  'gzAFZxUpJ/X#Gl,}^,9$qNXklv4W%B0$8%6TAIfuY?k#ijRIE0QoFWEq785uS&S~');
define('LOGGED_IN_KEY',    ' $PN{tvjgVbN9Wd`{nNiM~37H9%i<RcE2W6vOAIr|dQ}]xon+v(YLV@*80y^Aq7z');
define('NONCE_KEY',        'Vtqy,aL-u|SbEwP;tw %tAR-P;JkGJ_C}1`C/`>nP9+@WKJga|>.j2`H-d~[j+sa');
define('AUTH_SALT',        '8y4(!X1FpX0CG9N[N@(mts# DrmF;!T%Y8]bFP!mK|M0@J5p*nc_Q&:z}Uf]jk7~');
define('SECURE_AUTH_SALT', '~x>o+EYU-FUp21C>}Ga;;BX^z{]lgb_Egan?4)8b.eg>a&i9d6zEHm3l+215>r1B');
define('LOGGED_IN_SALT',   'DTg^X$ z?=@+jwMra0guLS3f1_bv~c0*ux4Lu^ev325cpFdxj87:|@?amW>Gd,%Y');
define('NONCE_SALT',       '1z!9HlY*]1la:yrC1eLY-261G=nWUix2W!hj1Q E&:%j3$wnO/K%6.1fFmz8s*+[');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix  = 'cukwp';

/**
 * Para desenvolvedores: Modo de debug do WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://codex.wordpress.org/pt-br:Depura%C3%A7%C3%A3o_no_WordPress
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Configura as variáveis e arquivos do WordPress. */
require_once(ABSPATH . 'wp-settings.php');

define('FS_METHOD', 'direct');