printjson(db.getCollection('lines').find({}).sort({'ftsl1' : -1, 'fpp1' : -1}).limit(1).toArray());
//
db.getCollection('lines').getIndexes()

db.getCollection('verify').find({ftsl1 : 1644461682, md4_v_db : { $exists : true}, 
    $expr :  { $and : [{$eq : ['$md4_v_db', '$md4_v_f']}, {$eq :[{$strLenBytes : '$md4_v_db'}, 32]}] }})

db.getCollection('verify').find({ftsl1 : 1644461682, 'md4_v_db' : { $exists : true}, 
    '$expr' :  { '$and' : [{'$eq' : ['$md4_v_db', '$md4_v_f']}, {'$eq' :[{$strLenBytes : '$md4_v_db'}, 32]}] }})

db.getCollection('verify').find({ftsl1 : 1644461682, 'md4_v_db' : { $exists : true}, 
    '$expr' :  { '$and' : [{'$eq' : ['$md4_v_db', '$md4_v_f']}, {'$eq' :[{$strLenBytes : '$md4_v_db'}, 32]}] }})

db.getCollection('verify').find({ftsl1 : 1644461682, '$expr' : {'$eq' : ['$md4_v_db', '$md4_v_f']}})

// WRONG:!!!
db.getCollection('lines').findOne({'ftsl1' : 1644461682}, {'sort' : {'fpp1': -1}})


db.getCollection('lines').find({'ftsl1' : 1644461682}).sort({'fpp1': -1}).limit(1)

db.getCollection('lines').updateMany({'ftslt' : 1644461682}, { $unset : { ftslt : "" } }, {upsert : true})


db.getCollection('lines').findAndModify({query : {'ftsl1.0' : 1644461682}, update: {'$set' : {'ftsl1' : 1644461682}}, 'upsert' : true})
db.getCollection('lines').find({'ftsl1.0' : 1644461682})



// ***************
// latest eval in verify/cmd.txt
db.getCollection('lines').aggregate(
[
{ $match: {'ftsl1' : 1644461682}},
{ $group: { '_id' : 'maxfp', maxfp : {'$max' : "$fpp1"}} }
]);

db.getCollection('lines').aggregate(
[{ $group: { _id : '$ftsl1' , lines : { '$sum' : 1}, maxfp : {'$max' : "$fpp1"}} }  ]);

db.getCollection('lines').aggregate(
[{ $group: { _id : '$ftsl1' , filesizeInDB : { "$sum" : "$len" } , lines : { '$sum' : 1}, maxfp : {'$max' : "$fpp1"}} }  ]);

db.getCollection('lines').createIndex({'ftsl1' : -1, 'fp0' : -1, 'fpp1' : -1}, {'unique' : true})
db.getCollection('lines').createIndex({'ftsl1' : -1, 'fpp1' : -1})

// QID-lastPtr
db.getCollection('lines').find({}).sort({'fpp1' : -1, 'fts' : -1}).limit(1);


db.getCollection('lines').find({}).sort({'fpp1' : 1 }).limit(95134).forEach(function(r) {
    print(r.line.trim());
});

db.getCollection('lines').count()
// 95134

// mongo wsal --quiet -eval "db.getCollection('lines').find({}).sort({'fpp1' : 1}).limit(95134).forEach(function(r) { print(r.line.trim()); });" | openssl md5


/* head -n 86260 access.log | openssl md5
(stdin)= 067f46711ae9887d67ab64285aaab34a 
mongo wsal --quiet -eval "db.getCollection('lines').find({}).sort({'fts' : 1, 'fpp1' : 1}).limit(86260).forEach(function(r) { print(r.line.trim()); });" | openssl md5
(stdin)= 067f46711ae9887d67ab64285aaab34a
*/

/*
mongo wsal --quiet -eval "db.getCollection('lines').find({}).sort({'fts' : 1, 'fpp1' : 1}).limit(93035).forEach(function(r) { print(r.line.trim()); });"
*/
db.getCollection('lines').aggregate(
[{ $group: { _id : '$fts' , maxfp : {'$max' : "$fpp1"}} }  ]);


db.getCollection('lines').aggregate(
[{ $group: { _id : '$fts' , filesizeInDB : { "$sum" : "$llen" } , lines : { '$sum' : 1}, maxfp : {'$max' : "$fpp1"}} }  ]);

db.getCollection('lines').find({}).sort({'fp0' : 1}).limit(1);

db.getCollection('lines').createIndex({'fts' : -1, 'fpp1' : -1});
db.getCollection('lines').dropIndex  ('fpp1_-1_fts_-1');
db.getCollection('lines').dropIndex  ('fpp1_-1');

// picks up nulls:
db.getCollection('verify').findOne({'ftsl1' : 1644461682,   $expr : { $eq : [{$strcasecmp : ['$md4_v_f', '$md4_v_db']}, 0]}}, {'sort' : {'fpp1': -1}})

db.getCollection('verify').findOne({'ftsl1' : 1644461682,   $expr : { $and : [{ $eq : [{$strLenBytes : "$md4_v_f"}, 32] }, { $eq : [{$strcasecmp : ['$md4_v_f', '$md4_v_db']}, 0]}]}}, {'sort' : {'fpp1': -1}})

// still picks up nulls
db.getCollection('verify').findOne({'ftsl1' : 1644461682,   $expr : { $and : [{ $eq : [{$strLenBytes : "$md4_v_f"}, 32] }, { $eq : [{$strcasecmp : ['$md4_v_f', '$md4_v_db']}, 0]}]}}, {'sort' : {'fpp1': -1}})

// staring to work
db.getCollection('verify').findOne({$expr : { $eq : [{$strLenBytes : "$md4_v_f"}, 32] }} )

db.getCollection('lines').find({'ftsl1' : 1644461682},  {'projection' : {'fpp1' : 1}}).sort({'fpp1' : -1}).limit(1)

