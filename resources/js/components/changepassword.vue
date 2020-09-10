<template>
	<div >
	    <h2>Change Password</h2>        
        <div class="alert" :class="typeofmsg" v-if="showMessage">             
            <button type="button" class="close-btn" v-on:click="showMessage=false">&times;</button>
            <strong>{{ message }}</strong>
        </div>
            <div class="form-group">
                <label for="inputPassword">Current Password</label>
                <input
                    type="password" class="form-control" v-model="currentPassword"
                    name="password" id="inputCurrentPassword"/>
            </div>

            <div class="form-group">
                <label for="inputPassword">New Password</label>
                <input
                    type="password" class="form-control" v-model="newPassword"
                    name="password" id="inputNewPassword"/>
            </div>

            <div class="form-group">
                <label for="inputPassword">Confirm New Password</label>
                <input
                    type="password" class="form-control" v-model="confirmNewPassword"
                    name="password" id="inputConfirmNewPassword"/>
            </div>

	    <div class="form-group">
	        <a class="btn btn-primary" v-on:click.prevent="saveUser()">Change Password</a>
	    </div>
	</div>
</template>

<script type="text/javascript">
	module.exports={
		data: function(){
            return { 
                currentPassword: "",
                newPassword: "",
                confirmNewPassword: "",

                typeofmsg: "alert-success",
                showMessage: false,
                message: "",
            }
        },
	    methods: {
	        saveUser: function(){
	        	if(this.checkPassword()){
		            axios.patch('api/users/'+this.$store.state.user.id+'/changePassword',{
		            	'currentPassword':this.currentPassword,
		            	'confirmNewPassword':this.confirmNewPassword,
		            }).then(response=>{
                        this.typeofmsg = "alert-success";
                        this.message = "Password updated correctly";
                        this.showMessage = true;
		            }).catch(error => { 
                        this.typeofmsg = "alert-danger";
                        this.message = "Error updating password";
                        this.showMessage = true;
		            });
		        }else{
					this.typeofmsg = "alert-danger";
					this.message = "Passwords must match";
					this.showMessage = true;
		        }
	        },
            checkPassword: function(){
                return this.newPassword == this.confirmNewPassword;
            },            
        },
        mounted() {
		}
	}
</script>

<style scoped>	

</style>