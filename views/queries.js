// If all or most queries are errors or / or /?, AND the browser version is over a years old, and it's not already classed as a bot, 
// it is probably a bot

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
