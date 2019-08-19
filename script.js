var xhr=new XMLHttpRequest();
xhr.open("POST","http://demo.rvparkoffice.com/wp-admin/admin-ajax.php",true);
xhr.setRequestHeader("Content-Type","application/json");
xhr.send(JSON.stringify({
	action:"piranha_test", security:"piranha-secure-ajax"
}));




