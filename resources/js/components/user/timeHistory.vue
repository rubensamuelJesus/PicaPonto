<template>
  <!--<v-container fill-height fluid grid-list-xl>
    <v-layout justify-centerwrap>-->
        <!--<p>Total Working Time: {{totalTime}} seconds</p>
        <p><br>Average Time Working: {{averageTime}} seconds</p>-->
      <div>
        <div>
            <h3>Choose the month!</h3>
            <v-select item-value="last"  @input="getHours()" v-model="mesEscolhido" :items="months"></v-select>
        </div>
        <div>
          <bar-chart v-if="ver == 1"
            id="line" 
            :data="chartData" 
            colors='[ "#FF6384", "#36A2EB" ]' 
            resize="true" label="value" xkey="label">
          </bar-chart >
          <p v-if="ver == 1"><br>Total Time Worked --> {{totalTime}} hours</p>
          <p v-if="ver == 1"><br>Average Time Worked --> {{averageTime}} hours</p>
          <p v-if="ver == 1"><br>Total Estimated Time Worked --> {{totalHoursEstimated}} hours</p>
          <p v-if="ver == 0"><br>This month you have worked 0 hours</p>

        </div>
      </div>
    <!--</v-layout>
  </v-container> -->
</template>


<script type="text/javascript">    
  import moment from 'moment'
  import { BarChart } from 'vue-morris'
    export default {
        data: () => ({
          totalHistory: [],
          totalTimeInMonths: [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],
          totalTime: 0,
          averageTime: 0,
          averageCount: 0,
          monthDate: 0,
          ver : 2,
          chartData: [],
          totalHoursEstimated: 0,
          mesEscolhido: '',
          months : ['January', 'February', 'March', 'April', 'May','June', 'July', 'August', 'September','October', 'November', 'December'],
        }),
        methods: {
            getTimeWorking() {
              axios.get("api/getMyTimeWorking")
              .then(response=>{
                  this.totalHistory = response.data
                  //console.log(this.totalHistory)
                  /*this.totalHistory.map(item=>{
                    //this.totalTimeInMonths[moment(String(item.entered_at)).format('MM')] += moment(String(item.left_at)).diff(moment(String(item.entered_at)), 'seconds')
                    this.totalTime+= moment(String(item.left_at)).diff(moment(String(item.entered_at)), 'hours','minutes') 


                    this.totalTimeInMonths[Number(moment(String(item.entered_at)).format('DD'))] += moment(String(item.left_at)).diff(moment(String(item.entered_at)), 'hours','minutes') 


                    this.averageCount++
                    //console.log("item.entered_at")
                    //console.log(item.entered_at)
                    //console.log(moment(String(item.entered_at)).format('MM'))

                    //console.log(moment(item.entered_at).daysInMonth())

                    this.totalHoursEstimated = Number(moment(item.entered_at, "YYYY-MM-DD").daysInMonth())*8



                    //console.log(Date("m",strtotime(item.entered_at)))
                  })
                  this.averageTime = this.totalTime/this.averageCount

                  for (let i = 0; i < this.totalTimeInMonths.length; i++) { 
                    //console.log(i)
                    console.log(this.totalTimeInMonths[i])
                    this.chartData.push({label: i, value : this.totalTimeInMonths[i]})
                  }*/

              }).catch(error=>{
                  console.log("erro TIME")
                  console.log(error)
              })

            },
            getHours(){ 
              this.ver = 0;
              this.totalTime = 0;
              this.averageCount = 0;
              this.totalHoursEstimated = 0;
              this.chartData=[];
              this.totalTimeInMonths = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
              console.log("this.totalHistory");
              console.log(this.totalHistory);
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
                console.log("-----------------------------------------------------")
                this.averageTime = this.totalTime/this.averageCount
                for (let i = 0; i < this.totalTimeInMonths.length; i++) { 
                  //console.log(i)
                  console.log(this.totalTimeInMonths[i])
                  this.chartData.push({label: i, value : this.totalTimeInMonths[i]})
                }
              }
            },

        },
        components: {
          BarChart,
        },
        mounted() {
            this.getTimeWorking();
        }        
    }
</script>