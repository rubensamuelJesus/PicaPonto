<template>
  <div>
    <v-toolbar flat color="primary">
      <v-toolbar-title>My Actuators</v-toolbar-title>
      <v-divider class="mx-2" inset vertical></v-divider>
      <v-dialog v-model="dialog" max-width="500px">
        <v-card>
          <v-card-title>
            <span class="headline">{{ formTitle }}</span>
          </v-card-title>
        </v-card>
      </v-dialog>
    </v-toolbar>
    <v-data-table :headers="headers" :items="my_actuators" class="elevation-1">
      <template v-slot:items="props">
        <td class="text-xs">{{ props.item.room_id}}</td>
        <td class="text-xs">{{ props.item.name }}</td>
        <td class="text-xs">{{ props.item.value }}</td>
        <v-btn v-if="props.item.value > 0"  color="blue darken-2" dark class="mb-2" v-on:click.native="off(props.item)" >Turn off</v-btn>
        <v-btn v-else  color="blue darken-2" dark class="mb-2" v-on:click.native="on(props.item)" >Turn on</v-btn>
      </template>
    </v-data-table>
  </div>
</template>

<script>
  export default {
    data: () => ({
      dialog: false,
      headers: [
       // { text: 'Access Type', value: 'access_type' },
        { text: 'Room Id', value: 'room_id' },
        { text: 'Actuator Name', value: 'name' },
        { text: 'Actuator Value', value: 'value' },
        { text: 'Action', value: 'value' },
        //{ text: 'Protein (g)', value: 'protein' },
       // { text: 'Actions', value: 'name', sortable: false }
      ],
      my_actuators: [],
      min: 0,
      max: 1200,
      switch1: false,

      desserts: [],
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
      this.getMyActuators();
    },

    methods: {
      on: function (actuator) {
        console.log(actuator)
        axios.post('api/updateSensor/2', {
        name:actuator.name,value: '1'
        }).then((response) => {
          console.log("response   acessssssssssss")
          console.log(response.data[0])
      this.getMyActuators();
          //this.my_actuators=response.data;
        })
        .catch(error => { 
          console.log("error   acessssssssssss")
          console.log(error)
          this.my_actuators=[];
        })
      },

      off: function (actuator) {
        axios.post('api/updateSensor/2', {
          name: actuator.name,
          value: '0'
        })
        .then((response) => {
          console.log(response.data)
      this.getMyActuators();
          //this.my_actuators=response.data;
        })
        .catch(error => { 
          console.log("error   acessssssssssss")
          console.log(error)
          this.my_actuators=[];
        })
      },

      getMyActuators (type='actuator') {
        axios.get('api/myActuators/'+type)
                    .then(response => {
                      console.log("response   acessssssssssss")
                      console.log(response.data[0])
                      this.my_actuators=response.data;
                    })
                    .catch(error => { 
                      console.log("error   acessssssssssss")
                      console.log(error)
                      this.my_actuators=[];
                    })
      },
    }
  }
</script>