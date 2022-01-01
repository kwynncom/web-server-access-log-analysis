printjson(db.getCollection('usage').aggregate(
[         { $match : { 'timed.time' : { '$gte' : 1633065493   } }}, 
          { $group : { _id : {  'agent' : '$agent', 'ip' : '$ip', 'email' : '$email' } }}   ]).toArray())
// *******
printjson(db.getCollection('lines').aggregate(
[
   { $match: {'url' : /sync/, 'ts' : {'$gte' : 1640863167}}},
   { $group: {  '_id' : { 'agent' : '$agent' },  countAll : { $sum: 1} }},
   { $sort : {'countAll' : -1, '_id.agent' : 1}}
] 
).toArray())

// as of ca Fri Dec 31 01:26:26 AM EST 2021
db.getCollection('lines').count({'ts' : {'$gte' : 1640863167}});
// 32478
db.getCollection('lines').count({'url' : /sync/, 'ts' : {'$gte' : 1640863167}});
// 23188


// ***************
// user agent below
// // If all or most queries are errors or / or /?, AND the browser version is over a years old, and it's not already classed as a bot, 
// it is probably a bot

// I can do the following with IP addresses, too.

// Better yet, if all queries of an agent are either the home page or an error, it's a bot to the extent that I don't care about it.  Given that 
// I'm processing my entire data set, it's all I need to know.  That, if something changes, then the IP address or agent will change status.

// Maybe the best I can do is compare these 2 queries for a perfect match:

printjson(db.getCollection('lines').aggregate(
[
   { $match: { '$or' : [{'url' : '/'}, {'url' : /^\/\?/}, {'httpCode' : {'$gte' : 400}}]}},
   { $group: {  '_id' : { 'agent' : '$agent' },  countPossBad : { $sum: 1} }},
   { $sort : {'countPossBad' : -1, '_id.agent' : 1}}
] 
).toArray())

printjson(db.getCollection('lines').aggregate(
[
   { $group: {  '_id' : { 'agent' : '$agent' },  countAll : { $sum: 1} }},
   { $sort : {'countAll' : -1, '_id.agent' : 1}}
] 
).toArray())


// https://www.javamadesoeasy.com/2017/03/how-to-use-if-else-in-mongodb.html

// This is it, or close; it's close but not close enough
printjson(db.getCollection('lines').aggregate(
[
   { $match: { '$or' : [{'url' : '/'}, {'url' : /^\/\?/}, {'httpCode' : {'$gte' : 400}}]}},
   { $project : { 'agent' : true, 'isErr' : {'$cond' : { if : { $gte : ['$httpCode' , 400]}, then : 1, else: 0}}}},                    
   { $group: {  '_id' : { 'agent' : '$agent' },  'countErr' : {$sum : '$isErr'}, 'countAll' : { $sum: 1} }},
   { $sort : {'countErr' : -1, '_id.agent' : 1}}
] 
).toArray())


// close to what I'm looking for
printjson(db.getCollection('lines').aggregate(
[
   { $match: { '$or' : [{'url' : '/'}, {'url' : /^\/\?/}, {'httpCode' : {'$gte' : 400}}]}},
   { $project : { 'agent' : true, 'isErr' : {'$cond' : { if : { $gte : ['$httpCode' , 400]}, then : true, else: false}}}},                    
   { $group: {  '_id' : { 'agent' : '$agent', 'isErr' : '$isErr' }, 'count' : { $sum: 1} }},
   { $sort : {'count' : -1, '_id.agent' : 1}}
] 
).toArray())

printjson(db.getCollection('lines').aggregate(
[
   { $match: { '$or' : [{'url' : '/'}, {'url' : /^\/\?/}, {'httpCode' : {'$gte' : 400}}]}},
   { $group: {  '_id' : { 'agent' : '$agent'}, 'count' : { $sum: 1} }},
   { $sort : {'count' : -1, '_id' : 1}}
] 
).toArray())

printjson(db.getCollection('lines').aggregate(
[
   { $match: { '$or' : [{'url' : '/'}, {'url' : /^\/\?/}]}},
   { $group: {  '_id' : {'url' : '$url'}, count: { $sum: 1} }},
   { $sort : {'count' : -1, '_id' : 1}}
] 
).toArray())

// *******
printjson(db.getCollection('lines').aggregate(
[
   { $group: { _id : { "ip" : "$ip", "url" : "$url" }, count: { $sum: 1} } },
   { $sort : {'count' : -1, '_id' : 1}}
] 
).toArray())

printjson(db.getCollection('lines').aggregate(
[
   { $match: {'url' : '/'} },
   { $group: { _id : { "ip" : "$ip", "url" : "$url" }, count: { $sum: 1} } },
   { $sort : {'count' : -1, '_id' : 1}}
] 
).toArray())

printjson(db.getCollection('lines').aggregate(
[
   { $match: {'agent' : 'Mozilla/5.0 (Windows; U; Windows NT 5.2; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.99 Safari/533.4'} },
   { $group: { _id : { "ip" : "$ip", "agent" : "$agent", "url" : "$url" }, count: { $sum: 1} } },
   { $sort : {'count' : -1, '_id' : 1}}
] 
).toArray())
