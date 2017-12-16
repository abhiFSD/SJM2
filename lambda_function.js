

exports.handler = (event, context, callback) => {
var http = require("http");
 
var options = {
  hostname: 'ec2-52-65-167-39.ap-southeast-2.compute.amazonaws.com',
  port: 80,
  path: '/index.php/lambda2/receiveSQS',
  method: 'POST',
  headers: {
      'Content-Type': 'application/json',
  }
};
var req = http.request(options, function(res) {
  console.log('Status: ' + res.statusCode);
  console.log('Headers: ' + JSON.stringify(res.headers));
  res.setEncoding('utf8');
  res.on('data', function (body) {
    console.log('Body: ' + body);
  });
});
req.on('error', function(e) {
  console.log('problem with request: ' + e.message);
});
// write data to request body
req.write('{"string": "success"}');
req.end();
};