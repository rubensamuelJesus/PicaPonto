<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Pica Ponto</title>
        <!-- Latest compiled and minified CSS & JS -->
        <link rel="stylesheet" href="{{ URL::asset('css/app.css') }}">

    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">
    <link href="https://unpkg.com/vuetify/dist/vuetify.min.css" rel="stylesheet">
        <style type="text/css">
            .nav-link {
                color: white;
            }
            .nav-link:hover {
                color: lightgray;
            }
            main > .container {
                padding: 40px 0 20px 0;
            }
        </style>

    </head> 
    <body>
        <div id="app">
          <v-app id="inspire" dark>
            <v-navigation-drawer
              clipped
              fixed
              v-model="drawer"
              app>
              <v-list dense>
                <v-list-tile @click="" v-if="this.$store.state.user && this.$store.state.user.type === 'admin'">
                  <v-list-tile-action>
                    <v-icon>people</v-icon>
                  </v-list-tile-action>
                  <v-list-tile-content>
                    <router-link to="/userList">
                        <v-list-tile-title class="white--text">User List</v-list-tile-title>
                    </router-link>
                  </v-list-tile-content>
                </v-list-tile>

                <v-list-tile @click="" v-if="this.$store.state.user && this.$store.state.user.type === 'admin'">
                  <v-list-tile-action>
                    <v-icon>house</v-icon>
                  </v-list-tile-action>
                  <v-list-tile-content>
                    <router-link to="/roomList">
                        <v-list-tile-title class="white--text">Room List</v-list-tile-title>
                    </router-link>
                  </v-list-tile-content>
                </v-list-tile>

                <v-list-tile @click="" v-if="this.$store.state.user && this.$store.state.user.type === 'admin'">
                  <v-list-tile-action>
                    <v-icon>lock</v-icon>
                  </v-list-tile-action>
                  <v-list-tile-content>
                    <router-link to="/accessPermissions">
                        <v-list-tile-title class="white--text">Access Permissions</v-list-tile-title>
                    </router-link>
                  </v-list-tile-content>
                </v-list-tile>

                <!--<v-list-tile @click="" v-if="this.$store.state.user && this.$store.state.user.type === 'admin'">
                  <v-list-tile-action>
                    <v-icon>history</v-icon>
                  </v-list-tile-action>
                  <v-list-tile-content>
                    <router-link to="/accessHistory">
                        <v-list-tile-title class="white--text">Access History</v-list-tile-title>
                    </router-link>
                  </v-list-tile-content>
                </v-list-tile>-->

                <v-list-tile @click="" v-if="this.$store.state.user">
                  <v-list-tile-action>
                    <v-icon>graphic_eq</v-icon>
                  </v-list-tile-action>
                  <v-list-tile-content>
                    <router-link to="/my_sensors">
                        <v-list-tile-title class="white--text">Sensors</v-list-tile-title>
                    </router-link>
                  </v-list-tile-content>
                </v-list-tile>

                <v-list-tile @click="" v-if="this.$store.state.user">
                  <v-list-tile-action>
                    <v-icon>toys</v-icon>
                  </v-list-tile-action>
                  <v-list-tile-content>
                    <router-link to="/my_actuators">
                        <v-list-tile-title class="white--text">Actuators</v-list-tile-title>
                    </router-link>
                  </v-list-tile-content>
                </v-list-tile>

                <v-list-tile @click="" v-if="this.$store.state.user">
                  <v-list-tile-action>
                    <v-icon>history</v-icon>
                  </v-list-tile-action>
                  <v-list-tile-content>
                    <router-link to="/myHistory">
                        <v-list-tile-title class="white--text">My History</v-list-tile-title>
                    </router-link>
                  </v-list-tile-content>
                </v-list-tile>

                 <v-list-tile @click="" v-if="this.$store.state.user">
                  <v-list-tile-action>
                    <v-icon>insert_chart_outlined</v-icon>
                  </v-list-tile-action>
                  <v-list-tile-content>
                    <router-link to="/timeHistory">
                        <v-list-tile-title class="white--text">My Time History</v-list-tile-title>
                    </router-link>
                  </v-list-tile-content>
                </v-list-tile>

                <v-list-tile @click="" v-if="this.$store.state.user && this.$store.state.user.type === 'admin'">
                  <v-list-tile-action>
                    <v-icon>insert_chart</v-icon>
                  </v-list-tile-action>
                  <v-list-tile-content>
                    <router-link to="/allTimeHistory">
                        <v-list-tile-title class="white--text">All Time History</v-list-tile-title>
                    </router-link>
                  </v-list-tile-content>
                </v-list-tile>

                <v-list-tile @click="" v-if="this.$store.state.user && this.$store.state.user.type === 'admin'">
                  <v-list-tile-action>
                    <v-icon>apps</v-icon>
                  </v-list-tile-action>
                  <v-list-tile-content>
                    <router-link to="/blockchain">
                        <v-list-tile-title class="white--text">BlockChain</v-list-tile-title>
                    </router-link>
                  </v-list-tile-content>
                </v-list-tile>
                <v-list-tile @click="" v-if="this.$store.state.user">
                  <v-list-tile-action>
                    <v-icon>settings</v-icon>
                  </v-list-tile-action>
                  <v-list-tile-content>
                    <router-link to="/profile">
                        <v-list-tile-title  class="white--text">Account Settings</v-list-tile-title>
                    </router-link>
                  </v-list-tile-content>
                </v-list-tile>
              </v-list>
            </v-navigation-drawer>
            <v-toolbar app fixed clipped-left>
              <v-toolbar-side-icon v-if="this.$store.state.user" @click.stop="drawer = !drawer"></v-toolbar-side-icon>
              <v-toolbar-title>Pica Ponto</v-toolbar-title>
              <v-spacer></v-spacer>
                <h4 id="labelName"v-if="this.$store.state.user" class="blue darken-3 font-weight-medium pa-3">@{{this.$store.state.user.name}}</h4>
              <v-spacer></v-spacer>
                 <time-working v-if="this.$store.state.user"></time-working>
                
                <div class="divBtnLogout">
                <v-btn id="btnLogout" v-if="this.$store.state.user" bottom color="primary" v-on:click.prevent="logout" >Logout</v-btn>
                </div>
              </v-toolbar>
            <v-content>
              <v-container fluid align-baseline>
                 <router-view>    </router-view>
             </v-container>
            </v-content>
            <v-footer app fixed>
              <span>&copy; 2019</span>
            </v-footer>
          </v-app>
        </div>
        <script src="js/app.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </body>

</html>
<style>
    #btnLogout {
      background-color: #1565c0 !important;
    }
    .divBtnLogout{
      display: table-cell;
      vertical-align: middle
    }
    .v-btn--bottom:not(.v-btn--absolute) {
        bottom: 0px !important;
    }
    #labelName{
      margin-bottom: 0px;
      padding: 10px 10px 10px 10px !important;
      border-radius: 2px !important;
    }
</style>