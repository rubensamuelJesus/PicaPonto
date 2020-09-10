<template>
  <div>
    <v-toolbar flat color="primary">
      <v-toolbar-title>My Sensors</v-toolbar-title>
      <v-divider
        class="mx-2"
        inset
        vertical
      ></v-divider>
      <v-btn fab color="white">
       <v-icon dark color="primary" @click="getMySensors('Analogic')">refresh</v-icon>
  </v-btn>
      <v-spacer></v-spacer>
      <v-switch
      light
        v-model="switch1"
        :label="`Showing: ${sensorType} Sensors`"
        @change="getMySensors(sensorType)"
      ></v-switch>
    </v-toolbar>
    <v-data-table
      :headers="headers"
      :items="my_sensors"
      class="elevation-1">
      <template v-slot:items="props">
        <!-- <td>{{ props.item.name }}</td> -->
        <td class="text-xs">{{ props.item.room_id}}</td>
        <td class="text-xs">{{ props.item.name }}</td>
        <td class="text-xs">{{ props.item.value }}</td>
        <td class="text-xs">{{ props.item.state }}</td>
        <td >
          <v-icon large :style ="{color:props.item.state == 'Danger' ? 'red' : 'green'}" >{{props.item.state == 'Danger' ? 'error' : 'done'}}</v-icon>
        </td>
        <td class="justify-center layout px-0">
        	<v-btn color="blue darken-2" dark class="mb-2" v-on:click.native="getSensorHistory(props.item.id)" >Check Graph</v-btn>
        </td>
      </template>
      <template v-slot:no-data>
        <!--<v-btn color="primary" @click="initialize">Reset</v-btn>-->
      </template>
    </v-data-table>

    <line-chart 
      id="line" 
      :data="chartData" 
      colors='[ "#FF6384", "#36A2EB" ]' 
      resize="true" label="sensor_id" ykey="['value']" xkey="label">
    </line-chart >

  </div>
  
  

</template>

<script>
import { LineChart } from 'vue-morris'
  export default {
    data: () => ({
      dialog: false,
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
      teste:['1','2','3'],
      min: 0,
      max: 1200,
      switch1: false,
      danger:false,
      desserts: [],
      sensorHistory:[],
      editedIndex: -1,
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
        LineChart,
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
    },

    methods: {
      getMySensors (type='Analogic') {
        axios.get('api/mySensors/'+type)
                    .then(response => {
                      console.log("response   acessssssssssss")
                      console.log(response.data[0])
                      this.my_sensors=response.data;
                    })
                    .catch(error => { 
                      console.log("error   acessssssssssss")
                      console.log(error)
                      this.my_sensors=[];
                    })
		
      //this.getSensorHistory ();                    
      },

      getSensorHistory (id) {
      	 this.chartData=[];
        axios.get('api/sensorHistory/'+id)
                    .then(response => {
                      console.log("response.data[0]");
                      console.log(response.data);
                      this.sensorHistory=response.data;

                      this.sensorHistory.map(item=>{
                       //console.log(item.value);
                       this.chartData.push({label: item.created_at, value : item.value, sensor_id:item.sensor_id},
                                                  )
                      })
                      
                      
                      console.log("!!!!!!!!!!!!!!!!!!!!")
                    })
                    .catch(error => { 
                      console.log("error   acessssssssssss")
                      console.log(error)
                    })
      },

      editItem (item) {
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.my_sensors.indexOf(item)
        confirm('Are you sure you want to delete this Register ?') && this.my_sensors.splice(index, 1)
        /*axios.delete('api/deleteAccess/'+ item.id)
        .then(response => {
          //console.log("response   acessssssssssss")
          //console.log(response)
        })
        .catch(error => { 
          //console.log("error   acessssssssssss")
          //console.log(error)
        })*/
      },

      close () {
       /* this.dialog = false
        setTimeout(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        }, 300)*/
      },

      save () {
       /* if (this.editedIndex > -1) {
          Object.assign(this.desserts[this.editedIndex], this.editedItem)
        } else {
          this.desserts.push(this.editedItem)
        }
        this.close()*/
      }
    }
  }
</script>