const express = require("express");
const mysql = require("mysql2");
const cors = require("cors");
const bodyParser = require("body-parser");
const path = require("path");

const app = express();

app.use(cors());
app.use(bodyParser.json());

app.use(express.static("public"));

/* =========================
   DATABASE CONNECTION
========================= */

const db = mysql.createConnection({

    host: "localhost",

    user: "root",

    password: "",

    database: "database1"

});

db.connect((err) => {

    if(err){

        console.log("Database Error :", err);

    }else{

        console.log("MySQL Connected");

    }

});

/* =========================
   LOGIN API
========================= */

app.post("/login", (req, res) => {

    const { username, password } = req.body;

    const sql = `
        SELECT * FROM users
        WHERE username = ?
        AND password = ?
    `;

    db.query(sql, [username, password], (err, result) => {

        if(err){

            return res.json({
                success:false,
                message:"Database Error"
            });

        }

        if(result.length > 0){

            res.json({
                success:true,
                message:"Login Success",
                user:result[0]
            });

        }else{

            res.json({
                success:false,
                message:"Username หรือ Password ไม่ถูกต้อง"
            });

        }

    });

});

/* =========================
   START SERVER
========================= */

app.listen(3000, () => {

    console.log("Server Running http://localhost:3000");

});