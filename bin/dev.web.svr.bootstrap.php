<?php
/* <
 * 
 */

# document root
#
#
$document_root= str_replace('\\','/', $_SERVER['DOCUMENT_ROOT']);

# request raw url path
#
#
$request_uri  = $_SERVER ['REQUEST_URI'];
$request_file = strtolower(@array_pop (explode ('/',$request_uri)));


# print_r($_SERVER);print_r($request_file);echo"\n";die($request_uri);


# normal file
#
#
$ar_normal_file = [
					'.html'		=> 'text/html'					,
					'.css'		=> 'text/css'					,
					'.js'		=> 'application/javascript'		,
					'.json'		=> 'application/json'			,
					'.png'		=> 'image/png'					,
					'.jpeg'		=> 'image/jpeg'					,
					'.jpg'		=> 'image/jpeg'					,
					'.gif'		=> 'image/gif'					,
					'.txt'		=> 'text/plain'					,
					'.pdf'		=> 'application/pdf'			,
					'.xml'		=> 'application/xml'			,
					'.svg'		=> 'image/svg+xml'				,
					'.woff'		=> 'font/woff'					,
					'.woff2'	=> 'font/woff2'					,
					'.svg'      => 'image/svg+xml'				,
					'.otf'		=> 'font/otf'					,
					'.ttf'      => 'font/ttf'					,
				];
foreach ($ar_normal_file as $one_file_ex => $one_file_ex_content_type)
{
	if (strpos ($request_file,$one_file_ex) !== false /* response static local file resource to client browser */)
	{
		# $request_local_file = 'public'.(explode('?',$request_uri) [0]);
		#
		#
		$request_local_file = $document_root.(explode('?',$request_uri) [0]);header ('Content-Type: ' . $one_file_ex_content_type);readfile($request_local_file);die;
	}
}

# replace /index.php/ => /
#
#
# $request_uri = str_replace('/index.php/','/',$request_uri);

# php file
#
#
{
	# URL query parameters with a question mark url => http://192.168.8.192/api/role/list?pageNo=1&pageSize=1
	#
	#
	$_SERVER['PATH_INFO'] = explode('?',$request_uri)[0];

	# $_SERVER['PHP_SELF'] = '/index.php' . $request_uri;
	#
	#
	# $_SERVER['REQUEST_URI'] = '/index.php' . $request_uri;

	# $_SERVER['SCRIPT_FILENAME'] = str_replace([ '\\'],'/',__DIR__) . '/public/index.php';
	#
	#
	# $_SERVER['SCRIPT_NAME'] = '/index.php';
}

# $request_local_file = $document_root.(explode('?',$request_uri) [0]);
#
#
$request_local_file = $document_root . $_SERVER['SCRIPT_NAME'];
if (file_exists($request_local_file))
{
	return require ($request_local_file);
}
else
{
	die('file not found : ' . $request_local_file);
}
#
#
# return require ('public/index.php');
/* > */