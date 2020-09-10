<template>
  <div>
    <v-toolbar flat color="primary">
      <v-toolbar-title>My Access History</v-toolbar-title>
      <v-divider
        class="mx-2"
        inset
        vertical
      ></v-divider>
      <v-btn fab color="white">
       <v-icon dark color="primary" @click="getMyHistory()">refresh</v-icon>
  </v-btn>
    </v-toolbar>
    <v-data-table
      :headers="headers"
      :items="myHistory"
      class="elevation-1"
    >
      <template v-slot:items="props">
        <td class="text-xs">{{ props.item.room_id }}</td>
        <td class="text-xs">{{ props.item.entered_at }}</td>
        <td class="text-xs">{{ props.item.left_at }}</td>
        <td class="text-xs">{{ props.item.time_in_room }}</td>
      </template>
    </v-data-table>
  </div>

</template>

<script>
import { BarChart } from 'vue-morris'
  export default {
    data: () => ({
      dialog: false,
      headers: [
        { text: 'Room Id ', value: 'room_id' },
        { text: 'Entered', value: 'entered_at' },
        { text: 'Left', value: 'left_at' },
        { text: 'Time', value: 'time_in_room' },
      ],
      myHistory:[],
    }),
    components: {
        BarChart,
      },

    mounted () {
      this.getMyHistory();
    },

    methods: {
      getMyHistory () {
        axios.get('api/myHistory').then(response=>{
          console.log("!!!!!!!!!!!!!!!!")
          console.log(response)
          this.myHistory=response.data
        }).catch(error=>{
          this.myHistory=[]
        })

      },
    }
  }
</script>