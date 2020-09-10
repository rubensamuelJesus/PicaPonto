<template>
  <div>
    <div>
      <h3>Check all the hours from users!</h3>
      <bar-chart 
        id="graficoHorasTodos" 
        :data="chartData" 
        colors='[ "#FF6384", "#36A2EB" ]' 
        resize="true" label="value" xkey="label">
      </bar-chart >

    <div>
        <h3>Choose the user and the month!</h3>
        <v-select item-value="last" v-model="userEscolhido" :items="namesUsers"></v-select>
        <v-select item-value="last" v-model="mesEscolhido" :items="months"></v-select>
        <p v-if="ver == 0"><br>This month you have worked 0 hours </p>
        <v-btn color="blue darken-2" dark class="mb-2" v-on:click.native="getAllHours()">Check Hours</v-btn>
    </div>

      <bar-chart v-if="ver == 1"
        id="graficoHorasSeparados" 
        :data="chartData1" 
        colors='[ "#FF6384", "#36A2EB" ]' 
        resize="true" label="value" xkey="label1">
      </bar-chart >
    </div>


  </div>
  
  

</template>

<script>
import { BarChart } from 'vue-morris'
import moment from 'moment'
  export default {
    data: () => ({
      dialog: false,
      totalHistory: [],
      totalTime: 0,
      headers: [
       // { text: 'Access Type', value: 'access_type' },
        { text: 'Room Id', value: 'room_id' },
        { text: 'Sensor Name', value: 'name' },
        { text: 'Sensor Value', value: 'value' },
        { text: 'Sensor State', value: 'state' },
        //{ text: 'Protein (g)', value: 'protein' },
       // { text: 'Actions', value: 'name', sortable: false }
      ],
      my_sensors: [],
      chartData: [
       //{ label: 'Red', value: 300 },
       //{ label: 'Blue', value: 50 },
       //{ label: 'Yellow', value: 100 }
      ],

      ver : 0,
      totalTime : 0,
      averageCount : 0,
      totalHoursEstimated : 0,
      chartData1 : [],
      totalTimeInMonths : [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],


      horasTodosTrabalhadores: [
       //{ label: 'Red', value: 300 },
       //{ label: 'Blue', value: 50 },
       //{ label: 'Yellow', value: 100 }
      ],
      teste:['1','2','3'],
      min: 0,
      max: 1200,
      switch1: false,
      danger:false,
      desserts: [],
      usersID: [],
      timeHistory:[],
      months : ['January', 'February', 'March', 'April', 'May','June', 'July', 'August', 'September','October', 'November', 'December'],
      mesEscolhido: '',
      userEscolhido:'',
      users:[],
      editedIndex: -1,
      userIdGraf: 0,
      namesUsers: [],
      idUserEscolhido: null,
      totalHistory : [],
      ver : 0,
      editedItem: {
        name: '',
        rfid_id: 0,
        id: 0,
        created_at: 0,
        protein: 0
      },
      defaultItem: {
        name: '',
        rfid_id: 0,
        id: 0,
        created_at: 0,
        protein: 0
      }
    }),
    components: {
        BarChart,
      },
    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
      },
      sensorType () {
        return this.switch1==true ? 'Digital' : 'Analogic'
      }
    },

    watch: {
      dialog (val) {
        val || this.close()
      }
    },

    /*created () {
      this.initialize()
    },*/
    mounted () {
      this.getMySensors();
      this.getAllUsers();
    },
    methods: {
      getMySensors () {
        this.chartData=[];
        axios.get('api/getAllTimeWorking')
        .then(response => {
          this.totalHistory = response.data
          //console.log("this.totalHistory!!!!!!!!!!!!!!!!!!!!")
        //console.log( this.totalHistory[5])
          for(let variable  in this.totalHistory){
            this.totalTime = 0
            //console.log( this.totalHistory[variable])
            this.totalHistory[variable].map(item=>{
                this.totalTime+=moment(String(item.left_at)).diff(moment(String(item.entered_at)), 'seconds')
                this.userIdGraf = item.user_id
            })
            this.chartData.push({label: this.userIdGraf, value : this.totalTime})
            //console.log(this.totalTime)

            }
        })
        .catch(error => { 
          console.log("error   acessssssssssss")
          console.log(error)
          this.my_sensors=[];
        })
    
      //this.getSensorHistory ();                    
      },
      getAllHours(){
        this.ver = 0;
        this.totalTime = 0;
        this.averageCount = 0;
        this.totalHoursEstimated = 0;
        this.chartData1=[];
        this.totalTimeInMonths = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
        this.users.map(item=>{
          if(item.name ==  this.userEscolhido){
              axios.get('api/getAllTimeWorking/'+item.id)
              .then(response => {
                console.log("response   acessssssssssss")
                console.log(response.data)

                this.totalHistory = response.data



                this.totalHistory.map(item=>{
                //this.totalTimeInMonths[moment(String(item.entered_at)).format('MM')] += moment(String(item.left_at)).diff(moment(String(item.entered_at)), 'seconds')
                let month = this.months.indexOf(this.mesEscolhido );
                console.log(month ? month + 1 : 1)
                if(Number(moment(String(item.entered_at)).format('MM')) == (month ? month + 1 : 1))
                {
                  this.totalTime+= moment(String(item.left_at)).diff(moment(String(item.entered_at)), 'hours','minutes') 
                  this.totalTimeInMonths[Number(moment(String(item.entered_at)).format('DD'))] += moment(String(item.left_at)).diff(moment(String(item.entered_at)), 'hours','minutes') 
                  this.averageCount++
                  //console.log("item.entered_at")
                  //console.log(item.entered_at)
                  console.log()

                  //console.log(moment(item.entered_at).daysInMonth())

                  this.totalHoursEstimated = Number(moment(item.entered_at, "YYYY-MM-DD").daysInMonth())*8
                  this.ver = 1
                }
                //console.log(Date("m",strtotime(item.entered_at)))
              })
              if(this.ver==1)
              {
                console.log("totalTimeInMonths")
                console.log(this.totalTimeInMonths)
                console.log("-----------------------------------------------------")
                this.averageTime = this.totalTime/this.averageCount
                for (let i = 0; i < this.totalTimeInMonths.length; i++) { 
                  //console.log(i)
                  console.log("this.totalTimeInMonths[i]")
                  console.log(this.totalTimeInMonths[i])
                  this.chartData1.push({label1: i, value : this.totalTimeInMonths[i]})
                }
              }
              else{
                this.ver = 0;
              }
              })
              .catch(error => { 
                console.log("error   acessssssssssss")
                console.log(error)
              }) 
            }
        })
      },

      getAllUsers () {
        axios.get('api/allUsers')
        .then(response => {
          console.log("response   acessssssssssss")
          //console.log(response)
          this.users=response.data;
          //console.log(this.allUsers);
          this.users.map(item=>{
            this.namesUsers.push(item.name)
            this.usersID.push(item.id)
          })

          //this.namesUsers=response.data;
          //console.log(this.namesUsers);
          //for (let i = 0; i < 8/*this.allUsers.lenght*/; i++) {
          //    //console.log("asdsdsadassdaasd"+this.allUsers[i].name);
          //    this.namesUsers[i] =  this.users[i].name;
           //   this.usersID[i] = this.users[i].id;
          //}
          

          /*this.allUsers.map(function(value, key) {
           this.namesUsers.push(value);
         });*/

          //this.allUsers.forEach(function(element) {
            //console.log(element.name);
            //this.namesUsers[0] = element.name;
            //this.namesUsers.push("5");
            //console.log(this.namesUsers);
            //this.$parent.namesUsers.push(element.name); // what to push unto the rows array?
          //console.log(this.namesUsers);
          //this.namesUsers.push({namesUsers}); // what to push unto the rows array?

        })
        .catch(error => { 
          console.log("error   acessssssssssss")
          console.log(error)
        })
      },
    }
  }
</script>