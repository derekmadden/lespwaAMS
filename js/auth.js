

function loginUser(){

	var xhr = new XMLHttpRequest();
	var data = new FormData(document.querySelector("form"));
	xhr.open("POST", "auth?toDo=login", true);
	xhr.addEventListener("readystatechange", function(){
		if(xhr.readyState === 4 && xhr.status === 200){
			if(xhr.responseText == "true"){
				window.location = "index";
			} else {
				// alert(xhr.responseText)
				alert("Incorrect email or password");
			}
		}
	})
	xhr.send(data);

}



function logoutUser(){

	var xhr = new XMLHttpRequest();
	var data = new FormData(document.querySelector("form"));
	xhr.open("POST", "auth?toDo=logout", true);
	xhr.addEventListener("readystatechange", function(){
		if(xhr.readyState === 4 && xhr.status === 200){
			window.location = "login.html";
		}
	})
	xhr.send(data);


}



function registerUser(){

	var xhr = new XMLHttpRequest();
	var data = new FormData(document.querySelector("form"));
	xhr.open("POST", "auth?toDo=register", true);
	xhr.addEventListener("readystatechange", function(){
		if(xhr.readyState === 4 && xhr.status === 200){
			if(xhr.responseText == "true"){
				window.location = "index";
			} else {
				// alert('not there');
				alert(xhr.responseText);
			}
		}
	})
	xhr.send(data);




}