<template>
    <div>
        <div class="level">
            <img :src="avatar" width="50" height="50" class="mr-1">
            <h1 v-html="user.name"></h1>   <!--<span v-text=ago></span>-->
        </div>
        <form v-if="canUpdate" method="POST" enctype="multipart/form-data">
            <!--<input type="file" name="avatar" accept="image/*" @change="onChange">-->   <!--mais validation sur serveur-->
            <!--<button type="submit" class="btn btn-primary">Add avatar</button>--> <!--no more needed-->
            <image-upload name="avatar" @loaded="onLoad"></image-upload> <!--name attribute merged with component root element-->
        </form>
    </div>
</template>

<script>
    //import moment from 'moment';
    //import gravatar from 'gravatar';
    import ImageUpload from './ImageUpload.vue';
    
    export default {
        props: ['user'],
        
        components: { ImageUpload },
        
        data() {
            return {
                avatar: this.user.avatar_path //NOT avatarPath
            }
        },
        
        computed: {
            canUpdate() {
                return this.authorize(user => user.id == this.user.id)
            },
            
            //ago() {
            //    return moment(this.user.created_at).fromNow();
            //}
        },
        
        mounted() {
            //function supportAjaxUploadWithProgress() {
            //    console.log(supportFileAPI() && supportAjaxUploadProgressEvents());
            //
            //    function supportFileAPI() {
            //        var fi = document.createElement('INPUT');
            //        fi.type = 'file';
            //        return 'files' in fi; //console.log(fi.files); //FileList object
            //    };
            //
            //    function supportAjaxUploadProgressEvents() {
            //        var xhr = new XMLHttpRequest();
            //        return !! (xhr && ('upload' in xhr) && ('onprogress' in xhr.upload));
            //    };
            //}
            //supportAjaxUploadWithProgress();
            
            //this.avatar = gravatar.url(this.user.email, {s: '200', r: 'pg', d: 'http://i.imgur.com/H357yaH.jpg'});
        },
        
        methods: {
            //onChange(e) { ////MOVED TO ImageUpload.vue
            //    if (! e.target.files.length) return;
            //    let avatar = e.target.files[0]; //file object
            //    
            //    let reader = new FileReader() //javascript api for reading the content of a file
            //
            //    //reader.readAsArrayBuffer(avatar); //readAsText, readAsBinaryString
            //    reader.readAsDataURL(avatar);
            //
            //    reader.onload = e => {
            //        //console.log(reader === e.target);
            //        //console.log(e.target.result.byteLength); //pour ArrayBuffer
            //        //console.log(e.target.result); //DataURL: data:image/jpeg;base64,/9j/4AAQSkZJRgABAQA
            //        //console.log(new Uint8Array(e.target.result)); //Int16Array //Int8Array
            //        this.avatar = e.target.result; //data url
            //        this.persist(avatar); //avatar = file object
            //
            //        ////https://www.base64encode.org/   //var num = 65; num.toString(2)  // 0b10000 = 16
            //        ////var u8 = new Uint8Array([65,66,67,68]);
            //        ////console.log(String.fromCharCode.apply(null, u8)); // A B C D // string created from the specified sequence of UTF-16 code units.
            //        ////console.log(btoa(String.fromCharCode.apply(null, u8))); //base64: QUJDRA==
            //        ////var u8 = new Uint8Array([255]); //255: outside of the Latin1 range
            //        ////var decoder = new TextDecoder('utf8');
            //        ////var b64encoded = btoa(decoder.decode(u8));
            //        //var u8 = new Uint8Array(e.target.result); //var u8 = new DataView(e.target.result);
            //        //var b64encoded = btoa(String.fromCharCode.apply(null, u8)); //string is plain ASCII and not multibyte Unicode/UTF-8
            //        ////console.log(b64encoded);
            //        //this.avatar = 'data:image/jpeg;base64,'+b64encoded;
            //        //
            //        //////back to Uint8Array:
            //        ////var u8_2 = new Uint8Array(atob(b64encoded).split("").map(function(c) {
            //        ////    return c.charCodeAt(0); }));
            //        ////// https://stackoverflow.com/questions/12710001/how-to-convert-uint8-array-to-base64-encoded-string                    
            //    };
            //},
            
            onLoad(avatar){
                this.avatar = avatar.src;
                this.persist(avatar.file);
            },
            
            persist(avatar){
                let data = new FormData(); //js api
                data.append('avatar', avatar);
                //console.log(data.get('avatar')); //file object
                axios.post(`/api/users/${this.user.name}/avatar`, {avatar: avatar}) //marche pas
                axios.post(`/api/users/${this.user.name}/avatar`, data)
                    .then(() => flash('Avatar uploaded!'));

                //var xhr = new XMLHttpRequest();
                //xhr.upload.addEventListener('progress', onprogressHandler, false);
                //xhr.open('POST', `/api/users/${this.user.name}/avatar`, true);
                //xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                //xhr.setRequestHeader('X-CSRF-TOKEN', document.head.querySelector('meta[name="csrf-token"]').content);
                ////xhr.setRequestHeader("Content-Type", "application/octet-stream"); //for being a good citizen
                ////xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");                
                ////xhr.setRequestHeader("Content-Type", "multipart/form-data"); //firefox guesses it if sending FormData object             
                //xhr.setRequestHeader("X-File-Name", avatar.name);
                //xhr.onload = function () {
                //    console.log('DONE', xhr.readyState); // readyState will be 4
                //    flash('Avatar uploaded!');
                //};
                ////console.log(avatar);
                ////xhr.send("foo=bar&lorem=ipsum"); //si x-www-form-urlencoded
                ////xhr.send(avatar); //File object, but I need a FormData object in my controller
                //xhr.send(data); //works. Absolutely needs the FormData API, however : "possible to do the encoding manually on the client. Writing a form data encoder yourself may be useful if you really want to learn every little detail about how forms work"
                //
                //function onprogressHandler(evt) {
                //    var percent = evt.loaded/evt.total*100;
                //    console.log('Upload progress: ' + percent + '%');
                //}
            }
        }
    }
</script>
