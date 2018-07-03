<template>
<transition name="list">
    <div :id="'reply-'+id" class="panel panel-default" v-if="show">
    <!--<div :id="'reply-'+id" class="panel panel-default">-->
        <div class="panel-heading">
            <div class="level">
                <h5 class="flex">
                    <a :href="'/profile/'+data.owner.name" v-text="data.owner.name">
                    </a> said <span v-text="ago"></span>
                </h5>
                
                <div v-if="signedIn">
                    <favorite :reply="data"></favorite>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div v-if="editing">
                <div class="form-group">
                    <textarea class="form-control" v-model="body"></textarea>  <!--pas besoin de mettre @{{ body }} -->
                </div>
                <button class="btn btn-xs btn-primary" @click="update">Update</button>
                <button class="btn btn-xs btn-link" @click="editing = false">Cancel</button>
            </div>
            <div v-else v-text="body"></div>
        </div>
        
        <!--<div class="panel-footer level" v-if="canUpdate">  -->   <!--WAS TRYING  can-update on <replies> in show.blade -->
        <!--<div class="panel-footer level" v-if="data.user_id == window.app.user.id">-->  <!-- non, peu pas utiliser window ici -->
        <!--<div class="panel-footer level" v-if="(user && this.data.user_id == user.id)">-->
        <div class="panel-footer level" v-if="canUpdate">  
            <button class="btn btn-xs mr-1" @click="editing = true">Edit</button>
            <button class="btn btn-xs btn-danger mr-1" @click="destroy">Delete</button>
        </div>
    </div>
</transition>
</template>

<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';
    
    export default {
        //props: ['data', 'canUpdate'],
        props: ['data'],
        
        components: { Favorite },
        
        data() {
            return {
                editing: false,
                id: this.data.id,
                body: this.data.body,
                user: window.App.user,
                show: true,
                
            };
        },
        
        computed: {
            ago() {
                return moment(this.data.created_at).fromNow() + '...';
            },
            
            signedIn() {
                return window.App.signedIn;
            },
            
            canUpdate() {
                //return this.data.user_id == window.App.user.id;    
                //return (window.App.user && this.data.user_id == window.App.user.id);
                //if (window.App.user && window.App.user.id == '55') {
                //    return true;
                //} else {
                //    return (window.App.user && this.data.user_id == window.App.user.id);
                //}            
                return this.authorize(user => this.data.user_id == user.id)
            }
            
            //csrftoken() {
            //    return document.head.querySelector('meta[name="csrf-token"]').content;
            //}
        },
        
        methods: {
            update() {
                axios.patch('/replies/' + this.data.id, {
                    body: this.body
                });
                
                this.editing = false;
                
                flash('Updated!');
            },
            
            destroy() {
                axios.delete('/replies/' + this.data.id);
                
                //$(this.$el).fadeOut(300, () => {
                //      flash('Your reply has been deleted');
                //});
                
                this.show = false;
                //this.$emit('deleted', this.data.id); //pas rap, c'est pas l'index
                this.$emit('deleted'); //data-centric approach
                //this.$emit('remove'); //NO, only captured by direct parent (Replies)
            }
        }
    }
</script>

<style>
    .list-enter {
        opacity: 0.7;
    }
    .list-enter-to {
        transition: all 0.8s;
        opacity: 1;
    }
    .list-leave-to {
        transition: all 0.8s;
        opacity: 0;
    }    
</style>
