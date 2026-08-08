function CheckEmail() {


    let email = document.getElementById("email").value;
    let xhttp = new XMLHttpRequest();


                 xhttp.onreadystatechange = function() {
                       
                    if (this.readyState == 4 && this.status == 200) {
            document.getElementById("emailresponse").innerHTML = this.responseText;
        }            
        else if (this.readyState == 4) {
            document.getElementById("emailresponse").innerHTML = "Error checking email";
        }
    }
    xhttp.open("POST", "../Controller/CheckEmail.php", true);
         
    xhttp.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    
    xhttp.send("email=" + email);
}