<template>
    <div>
        <div v-if="days < '0' &&  hours < '0' && minutes < '0' && seconds < '0' ">
            <p style="color:red;"> 
                0:0:0:0
            </p>
        </div>

        <div v-else class="divRoom" >
            <div class="divLeft" v-if="room">
                <p class="room"> {{ "Inside room "+ room +" since:"}} </p>
                <p>{{ date}}</p>
            </div>
            <div class="divLeft" v-else>
                <p class="room">You are not in a room</p>
            </div>
            <div class="verticalLine" v-if="room"></div>
            <div class="verticalLineNoTime" v-else></div>

            <div class="divRight" v-if="room">
                <p class="room"> {{ "Room : "+ room}} </p>
                <p> {{ Math.trunc(days)+"D" }} : {{ Math.trunc(hours)+"H" }} : {{ Math.trunc(minutes)+"M" }} : {{ Math.trunc(seconds)+"S" }}</p>
            </div>
            <div class="divRight" v-else>
                <p class="room">Enter a room to count the time</p>
            </div>
        </div>

    </div>
</template>

<script>
export default { 
    data() {
        return {
            now: 0,
            room: null,
        date: {
            type: String
        },
            count: 0,
        }
    },
    methods: {
        timer_loop() {
            this.count++
            this.now = Math.trunc(new Date().getTime() / 1000)
            //console.log(this.now);
            this.count < 200 && setTimeout(this.timer_loop, 1000)
        },
        getTimeWorking() {
            axios.get("api/getTimeWorking")
            .then(response=>{
                console.log("RESPOSTA TIME---------------------")
                console.log(response.data)
                this.date=response.data.entered_at
                this.room=response.data.room_id

            }).catch(error=>{
                console.log("erro TIME")
                console.log(error)
            })
        },

    },
    mounted() {
        this.timer_loop()
        this.getTimeWorking()
        this.sockets.subscribe('access_granted', (data) => {
            console.log("socketsssssssssss")
            console.log(data)
            this.timer_loop()
            this.getTimeWorking()
            //this.msg = data.message;
        });
    },

    computed: {
        seconds() {
            return (this.now - this.modifiedDate) % 60
                
        },

        minutes() {

            return ((this.now - this.modifiedDate) / 60) % 60

        },
        hours() {

            return ((this.now - this.modifiedDate) / 60 / 60) % 24

        },
        days() {

            return ((this.now - this.modifiedDate) / 60 / 60 / 24)

        },
        modifiedDate: function () {
            return Math.trunc(Date.parse(this.date) / 1000)
        }

    }
}
</script>

<style>
    p.room {
        margin-bottom: 0px;
        margin-top: 15px;
        text-align: center;
    }
    .divRoom{
    }
    .divRoom div{
        height: 100%;
        margin: auto;
        display: inline-block;
    }
    .divLeft{
        padding-right: 10px;  
    }
    .divRight {
        padding-left: 10px !important;

    } 
    .verticalLine {
        border-left: 6px solid #1565c0; 
        height: 35px !important;
        vertical-align : middle !important;
        margin-bottom: 19px !important;
    } 
    .verticalLineNoTime{
        border-left: 6px solid #1565c0; 
        height: 35px !important;
        vertical-align : middle !important;
        margin-bottom: 4px !important; 
    }
    
</style>