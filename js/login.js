async function login(){

    const username = document.getElementById("username").value;

    const password = document.getElementById("password").value;

    const message = document.getElementById("message");

    const response = await fetch("http://localhost:3000/login",{

        method:"POST",

        headers:{
            "Content-Type":"application/json"
        },

        body:JSON.stringify({

            username:username,
            password:password

        })

    });

    const data = await response.json();

    if(data.success){

        localStorage.setItem("user", JSON.stringify(data.user));

        window.location.href = "dashboard.html";

    }else{

        message.innerHTML = data.message;

    }

}