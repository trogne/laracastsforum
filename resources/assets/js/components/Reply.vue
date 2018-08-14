<template>
<transition name="list">
    <div :id="'reply-'+id" class="panel" :class="isBest ? 'panel-success' : 'panel-default'" v-if="show"> <!--v-if needed for transiton on delete-->
        <div class="panel-heading">
            <div class="level">
                <h5 class="flex">
                    <a :href="'/profile/'+reply.owner.name" v-text="reply.owner.name">
                    </a> said <span v-text="ago"></span>
                </h5>
                
                <div v-if="signedIn">
                    <favorite :reply="reply"></favorite>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div v-if="editing">
                <form @submit.prevent="update">
                    <div class="form-group">
                        <!--<textarea @keydown.enter.prevent @keyup.enter.prevent="update" id="sako" class="form-control" v-model="body" required></textarea>-->  <!--pas besoin de @{{ body }}  , et attention v-body != v-text  -->
                        <wysiwyg v-model="body"></wysiwyg>
                    </div>
                    <!--<button id="miko" class="btn btn-xs btn-primary" @click="update">Update</button>-->
                    <button class="btn btn-xs btn-primary">Update</button>
                    <button class="btn btn-xs btn-link" @click="editing = false" type="button">Cancel</button> <!--type button, not a submit button-->
                </form>
            </div>
            <div v-else v-html="body"></div> <!-- v-html au lieu de v-text -->
        </div>
        
        <!--<div class="panel-footer level" v-if="canUpdate">  -->   <!--WAS TRYING  can-update on <replies> in show.blade -->
        <!--<div class="panel-footer level" v-if="data.user_id == window.app.user.id">-->  <!-- non, peu pas utiliser window ici -->
        <!--<div class="panel-footer level" v-if="(user && this.data.user_id == user.id)">-->
        <!--<div class="panel-footer level" v-if="authorize('owns', reply) || authorize('owns', reply.thread)">-->
        <div class="panel-footer level" v-if="(authorize('owns', reply) || authorize('owns', reply.thread)) && !editing">
            <!--<div v-if="canUpdate">-->
            <!--<div v-if="authorize('updateReply', reply)">-->
            <div v-if="authorize('owns', reply)">
                <button class="btn btn-xs mr-1" @click="editing = true">Edit</button>
                <button class="btn btn-xs btn-danger mr-1" @click="destroy">Delete</button>                
            </div>
            <!--<button class="btn btn-xs btn-default ml-a" @click="markBestReply" v-show="!isBest">Best Reply?</button> <!--margin-left:auto, au lieu des 2 autres button dans un div.flex -->
            <!--<button class="btn btn-xs btn-default ml-a" @click="markBestReply" v-if="authorize('updatedThread', reply.thread)">Best Reply?</button>      -->
            <button class="btn btn-xs btn-default ml-a" @click="markBestReply" v-if="authorize('owns', reply.thread)">Best Reply?</button>      
        </div> 
    </div>
</transition>
</template>

<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';
    
    export default {
        //props: ['data', 'canUpdate'],
        props: ['reply'],
        
        components: { Favorite },
        
        data() {
            return {
                editing: false,
                id: this.reply.id,
                body: this.reply.body,
                user: window.App.user,
                show: true,
                isBest: this.reply.isBest, //not needed if using shared state
                //reply: this.data, //now called reply in the first place (props)
                //thread: window.thread //for shared state... re-evaluates when window.thread changes
            };
        },
        
        computed: {
            //isBest(){ //read from that shared state, works regardless of whether we're appending the custom accessor (getIsBestAttribute)
            //    //return window.thread.best_reply_id == this.id; //works too
            //    return this.thread.best_reply_id == this.id;
            //},
            
            ago() {
                return moment(this.reply.created_at).fromNow() + '...';
            },
            
            //signedIn() {
            //    return window.App.signedIn;
            //},
            
            ////Now in authorization.js
            //canUpdate() {
            //    //return this.data.user_id == window.App.user.id;    
            //    //return (window.App.user && this.data.user_id == window.App.user.id);
            //    //if (window.App.user && window.App.user.id == '55') {
            //    //    return true;
            //    //} else {
            //    //    return (window.App.user && this.data.user_id == window.App.user.id);
            //    //}            
            //    return this.authorize(user => this.data.user_id == user.id)
            //}
            
            //csrftoken() {
            //    return document.head.querySelector('meta[name="csrf-token"]').content;
            //}
        },
        
        created () { //not needed if using shared state
            window.events.$on('best-reply-selected', id => {
                this.isBest = (id === this.id);
            });
        },
        
        methods: {      //this.body = v-model
            update() {
                axios.patch('/replies/' + this.id, {body: this.body})
                    .catch(error => {
                        flash(error.response.data, 'danger'); //now catching the ValidationException in app/Exception/Handler.php
                        //flash(error.response.data.errors.body[0], 'danger'); //WITHOUT try/catch
                        this.body = this.reply.body; //back to the original reply body
                    });

                this.editing = false;
                flash('Updated!');
            },
            
            destroy() {
                axios.delete('/replies/' + this.id);
                
                //$(this.$el).fadeOut(300, () => {
                //      flash('Your reply has been deleted');
                //});
                
                this.show = false;                //this.$emit('deleted', this.data.id); //pas rap, c'est pas l'index
                this.$emit('deleted'); //data-centric approach
                //this.$emit('remove'); //NO, only captured by direct parent (Replies)
            },
            
            markBestReply(){
                //this.isBest = true; //global event instead, using our global bus
                
                axios.post('/replies/' + this.reply.id + '/best');
                
                window.events.$emit('best-reply-selected', this.reply.id);
                //////using shared state (way to go about it that doesn't involve firing an event, bu with that approach every reply have access to the thread... repeating logic over and over which isn't ideal... Vuex would be useful in a setup like this)
                ////window.thread.best_reply_id = this.id; //no, Vue isn't tracking window.thread, isBest won't re-evealuate... there is where things like Vuex come into play... or could assign thread: window.thread (but gets clunky...)
                //this.thread.best_reply_id = this.id; //this.thread not needed
            }
            //1- could have each reply determine whether it's best by looking at some component agnostic state - all looking into a store and checking to see if their id matches up with the best reply
            //2- event... - THAT
            
            //updateonenter() { //no more needed cause now form submission (même pas vrai! need vue event, see textarea)
            //    this.editing = true;
            //    setTimeout(() => {
            //        var input = document.getElementById("sako");
            //        input.addEventListener("keydown", function(event) {
            //            if (event.keyCode === 13 && event.shiftKey) {
            //                event.preventDefault();
            //                document.getElementById("miko").click(); //ok, but new line :(
            //            }
            //            return false;
            //        });                 
            //    },0); // works with 0, cause callback pushed to the event loop...  waits for stack to clear, then cb pushed to the stack
            //}
        }
    }
</script>

<style>
    .list-enter {
        opacity: 0.2;
    }
    .list-enter-to {
        transition: all 0.4s;
        opacity: 1;
    }
    .list-leave-to {
        transition: all 0.4s;
        opacity: 0;
    }    
</style>
