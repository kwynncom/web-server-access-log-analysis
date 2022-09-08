const http = require('http');
var MongoClient = require('mongodb').MongoClient;
var mongoConnURL = 'mongodb://localhost/';

const hostname = '127.0.0.1';
const port = 3000;

class mongoHello {
  constructor() {
    this.setMongoDB();
    this.htserver();

  }

  setMongoDB() {

    const self = this;

    MongoClient.connect(mongoConnURL, function(err, client) {
      var c = client.db('qemail').collection('usage');
      self.collo = c;

    });  
  }

  async getMongoRes() {

const q = [
          { $match : { 'timed.time' : { '$gte' : 1633065493   }, 'email' : {'$ne' : null} }}, 
          { $group : { _id : {  'agent' : '$agent', 'ip' : '$ip', 'email' : '$email'},
              'mints' : {'$min' : '$timed.time'},
              'maxts' : {'$max' : '$timed.time'}
               }}   ];
 


    const dbrr = await this.collo.aggregate(q).toArray();
    return JSON.stringify(dbrr, null, 2);
  }

  async doHTr(req, res) {
    res.statusCode = 200;
    res.setHeader('Content-Type', 'text/plain');
    const mr = await this.getMongoRes();
    res.end(mr);
    process.exit();
  }

  htserver() {
    const self = this;
    const server = http.createServer((req, res) => {
        self.doHTr(req, res);
    });

    server.listen(port, hostname, () => {
      console.log(`Server running at http://${hostname}:${port}/`);
    });

  } // func
} // class

new mongoHello();