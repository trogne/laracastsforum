let user = window.App.user;

//let authorizations = {
module.exports = {
    ////updateReply: function(reply){
    ////    return reply.user_id === user.id;
    ////}
    //updateReply(reply){
    //    return reply.user_id === user.id;
    //},
    //
    //updatedThread(thread){
    //    return thread.user_id === user.id;
    //}
    
    owns (model, prop = 'user_id'){
        //return model.user_id === user.id; //works too
        //return model['user_id'] === user.id;
        return model[prop] === user.id;
    },
    
    isAdmin () {
        return ['JohnDoe', 'JaneDoe', 'Fiso'].includes(user.name);
    }
};

//module.exports = authorizations;