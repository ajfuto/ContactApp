const urlBase = 'http://dev.mycontacts.lol/LAMPAPI';
const extension = 'php';

document.addEventListener('DOMContentLoaded', function () {
	readCookie();
});

let userId = 0;
let firstName = "";
let lastName = "";

function searchOnEnterReg()
{
	if(event.keyCode == 13)
	{
		register();
	}
}
function searchOnEnterLogin()
{
	if(event.keyCode == 13)
	{
		console.log("searchOnEnterLogin triggered")
		doLogin();
	}
}

function searchOnEnterSearch()
{
	if(event.keyCode == 13)
	{
		console.log("searchOnEnterSearch triggered");
		searchContact();
	}
}

function searchOnEnterAdd()
{
	if(event.keyCode == 13)
	{
		addContact();
	}
}

function liveSearchContacts()
{
	searchContact();
}


function doLogin()
{
	userId = 0;
	firstName = "";
	lastName = "";
	
	let login = document.getElementById("username").value;
	let password = document.getElementById("password").value;

	if (login == "" || password == "")
	{
		document.getElementById("loginResult").innerHTML = "please fill out each box completely";
		return;
	}
	// var hash = md5( password );
	
	document.getElementById("loginResult").innerHTML = "";

	let tmp = {login:login,password:password};
	// var tmp = {login:login,password:hash};
	let jsonPayload = JSON.stringify( tmp );
	
	let url = urlBase + '/Login.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				let jsonObject = JSON.parse( xhr.responseText );
				userId = jsonObject.id;
		
				// if the user is not in our database
				if(userId < 1)
				{		
					document.getElementById("loginResult").innerHTML = "incorrect username/password";
					return;
				}
		
				firstName = jsonObject.firstName;
				lastName = jsonObject.lastName;

				saveCookie();
	
				window.location.href = "contacts.html";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("loginResult").innerHTML = err.message;
	}

}

function register()
{
	firstName = document.getElementById("firstname").value
	lastName = document.getElementById("lastname").value
	let login = document.getElementById("username").value;
	let password = document.getElementById("password").value;

	if(firstName == "" || lastName == "" || login == "" || password == ""){
		document.getElementById("registerResult").innerHTML = "please fill out each box completely"
		return;
	}

	document.getElementById("registerResult").innerHTML = "";

	let tmp = {FirstName:firstName,LastName:lastName,Login:login,Password:password};
	// var tmp = {login:login,password:hash};
	let jsonPayload = JSON.stringify( tmp );
	
	let url = urlBase + '/Registration.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				let jsonObject = JSON.parse( xhr.responseText );
				error = jsonObject.error;
		
				// if the user is not in our database
				if (error !== "")
				{
					document.getElementById("registerResult").innerHTML = error;
					return;
				}

				saveCookie();
	
				window.location.href = "contacts.html";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("registerResult").innerHTML = err.message;
	}
}

function saveCookie()
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString();
}

function readCookie()
{
	userId = -1;
	let data = document.cookie;
	let splits = data.split(",");
	for(var i = 0; i < splits.length; i++) 
	{
		let thisOne = splits[i].trim();
		let tokens = thisOne.split("=");
		if( tokens[0] == "firstName" )
		{
			firstName = tokens[1];
		}
		else if( tokens[0] == "lastName" )
		{
			lastName = tokens[1];
		}
		else if( tokens[0] == "userId" )
		{
			userId = parseInt( tokens[1].trim() );
		}
	}
	console.log(userId);
	
	console.log(userId < 0 && window.location.href.indexOf("contacts.html") != -1);
	if( userId < 0 && window.location.href.indexOf("contacts.html") != -1)
	{
		console.log('invalid user ID, redirecting');
		window.location.href = "/";
	}
	else
	{
		document.getElementById("loadUserName").innerHTML = "hello, " + firstName + " " + lastName;
	}
}

function doLogout()
{
	userId = 0;
	firstName = "";
	lastName = "";
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}

function addContact()
{
	readCookie();
	let fname = document.getElementById("firstNameInput").value;
	let lname = document.getElementById("lastNameInput").value;
	let number = document.getElementById("phoneInput").value;
	let email = document.getElementById("emailInput").value;

	document.getElementById("contactAddResult").innerHTML = "";

	let tmp = {FirstName:fname,LastName:lname,Email:email,PhoneNumber:number,UserID:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/AddContact.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("contactAddResult").innerHTML = "contact has been added";
				let addModal = document.getElementById("addModal");
				addModal.style.display = "none";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("contactAddResult").innerHTML = err.message;
	}
	
}

function searchContact()
{
	console.log("search triggered");
	let srch = document.getElementById("searchText").value;

	console.log(srch);

	let tmp = {search:srch,userId:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/SearchContacts.' + extension;

	let table = document.getElementById("tableBody");
	let tBody = document.getElementById("tableBody");
	tBody.innerHTML = "";
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	console.log("request header set");

	//$("#contactsTable tbody tr").remove(); 
	try
	{
		console.log("inside try");
		xhr.onreadystatechange = function() 
		{
			console.log("inside function");
			if (this.readyState == 4 && this.status == 200) 
			{
				console.log("inside if");
				document.getElementById("searchResult").innerHTML = "contact(s) retrieved";
				let jsonObject = JSON.parse( xhr.responseText );
				console.log(jsonObject);
				
				let j = 0
				// let row;
				for( let i=0; i<jsonObject.results.length; i++ )
				{
					let row = table.insertRow();
					let currContact = jsonObject.results[i]
					console.log(currContact);
					// if(i%4==0)
					// {
					// 	j++;
					// 	row = table.insertRow(j);
					// }

					// console.log(jsonObject[i]);
					console.log(currContact.FirstName);
					let cell = row.insertCell();
					cell.innerHTML = currContact.FirstName;

					cell = row.insertCell();
					cell.innerHTML = currContact.LastName;

					cell = row.insertCell();
					cell.innerHTML = currContact.PhoneNumber;

					cell = row.insertCell();
					cell.innerHTML = currContact.Email;

					// row.insertCell(jsonObjects[i]["FirstName"]);
					// let cell = row.insertCell();
					// cell1.innerHTML = jsonObject.results[i];
				}
			}
			else
			{
				console.log("state" + this.readyState)
				console.log("status " + this.status)
			}
		};
		console.log(jsonPayload);
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		console.log('this is broken');
		document.getElementById("searchResult").innerHTML = "dummy";
	}
	
}

/*
function searchModalUp()
{
	// Modal Setup
	// Get the modal
	let searchModal = document.getElementById("searchModal");

	// Get the button that opens the modal
	let searchBtn = document.getElementById("searchBtn");

	// Get the <span> element that closes the modal
	let searchSpan = document.getElementsByClassName("close")[0];

	let searchNameButton = document.getElementById("searchNameButton");

	// When the user clicks the button, open the modal 
	searchBtn.onclick = function() {
	searchModal.style.display = "block";
	}

	// When the user clicks on <span> (x), close the modal
	searchSpan.onclick = function() {
	searchModal.style.display = "none";
	}

	searchNameButton.onclick = function() {
		searchModal.style.display = "none";
		//searchContact();
	}

	// When the user clicks anywhere outside of the modal, close it
	window.onclick = function(event) {
	if (event.target == searchModal) {
		searchModal.style.display = "none";
	}
	}
}
*/


function addModalUp()
{
	// Get the modal
	let addModal = document.getElementById("addModal");

	// Get the button that opens the modal
	let addBtn = document.getElementById("addBtn");

	let addSpan = document.getElementsByClassName("close")[0];
	// When the user clicks the button, open the modal 
	addBtn.onclick = function() 
	{
		addModal.style.display = "block";
	}

	// When the user clicks on <span> (x), close the modal
	addSpan.onclick = function() 
	{
		addModal.style.display = "none";
	}
	window.onclick = function(event) 
	{
		if (event.target == addModal) 
		{
			addModal.style.display = "none";
		}
	}
}

