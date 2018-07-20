//app.js

const mysql = require('mysql');
const connection = mysql.createConnection({
  host: 'localhost',
  user: 'sfeuser',
  password: 'sfepassword',
  database: 'sfedata'
});
connection.connect((err) => {
  if (err) throw err;
  console.log('Connected!');
});
