
/** users indexes **/
db.getCollection("users").ensureIndex({
  "_id": NumberInt(1)
},[
  
]);

/** users indexes **/
db.getCollection("users").ensureIndex({
  "username": NumberInt(1)
},{
  "unique": true
});

/** users indexes **/
db.getCollection("users").ensureIndex({
  "realname": NumberInt(1)
},[
  
]);

/** users indexes **/
db.getCollection("users").ensureIndex({
  "email": NumberInt(1)
},{
  "unique": true
});

/** users records **/
db.getCollection("users").insert({
  "avatar": "https:\/\/1.gravatar.com\/avatar\/3c727687ac031937714cadcce7c9418d?s=29",
  "email": "hankewins@gmail.com",
  "password": "21232f297a57a5a743894a0e4a801fc3",
  "realname": "易翰",
  "username": "hankewins",
  "_id": ObjectId("5268c20cf7a0d52028000001"),
  "updated": ISODate("2013-10-24T06:45:01.592Z"),
  "created": ISODate("2013-10-24T06:45:01.592Z"),
  "active": true,
  "__v": NumberInt(0)
});
db.getCollection("users").insert({
  "avatar": "https:\/\/1.gravatar.com\/avatar\/3c727687ac031937714cadcce7c9418d?s=29",
  "email": "admin@gionee.com",
  "password": "21232f297a57a5a743894a0e4a801fc3",
  "realname": "Administrator",
  "username": "admin",
  "_id": ObjectId("5268c379d4c3be6e28000001"),
  "updated": ISODate("2013-10-24T06:51:31.860Z"),
  "created": ISODate("2013-10-24T06:51:31.860Z"),
  "active": true,
  "__v": NumberInt(0)
});
