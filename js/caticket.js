
( function() {

	//config
	Vue.config.devtools = true;
	let restdomain="https://www.kirchenaadorf.ch/chrischona/caticket";

	Vue.component('eventbox', {
			template: '#eventbox',
			data () {
				return {
					loading: false,
					events: [],
					errors: []
				}
			},
			created () {
				// fetch the data when the view is created and the data is
				// already being observed
				this.fetchData()
			},
			watch: {
				// call again the method if the route changes
				'$route': 'fetchData'
			},
			filters: {
				day: function (date) {
					return moment(date).format('DD');
				},
				shortmonth: function (date) {
					return moment(date).format('MMM');
				}
			},
			methods: {
				selectEvent(eventid){
					this.errors = [];
					this.$parent.setEventid(eventid);
					const requestOptions = {
						method: "GET",
						headers: { "Content-Type": "application/json" },
					};
					fetch(restdomain+"/api/events/callforreservation/"+eventid, requestOptions)
						.then( async response => {
							if(response.ok) {
								let reservation= await response.json();
								console.log(reservation);
								this.$parent.setReservationkey(reservation.reservationkey);
								this.$parent.goToStep("register");
							}else if(response.status==406){
								this.errors.push('Das Anmelden ist leider nicht möglich.');

							}else if(response.status==410){
								this.errors.push('Das Anmelden ist leider nicht mehr möglich.');

							} else{
								this.errors.push('Es ist ein Serverfehler aufgetreten');
							}
						})
						.catch((e) => {
							console.log(e);
							this.errors.push('Es ist ein Render-Serverfehler aufgetreten');
						});

				},
				fetchData () {
					this.errors = [];
					this.evens = null;
					this.loading = true;

					const requestOptions = {
						method: "GET",
						headers: { "Content-Type": "application/json" }

					};
					fetch(restdomain+"/api/events/active", requestOptions)
						.then(response => {
							if(response.ok){
								return response.json()
							} else{
								this.errors.push('Es ist ein Serverfehler aufgetreten');
							}
						})
						.then(response => {
							this.events = response;
						})
						.catch((e) => {
							this.errors.push('Es ist ein Render-Serverfehler aufgetreten');
						});
				}
			}
		}
	);

	Vue.component('register', {
		template: '#registerbox',
		props: {
			timer: {
				default: -2,
			},
		},
		data() {
			return {
				loading: false,
				events: [],
				errors: [],
				formdata:{ref:-1},
				timerstep:0,
				oldentries:this.$parent.oldentries
			}
		},
		created(){
			this.checkReservationWindow ();

		},
		methods: {
			handleOk(bvModalEvt) {
				this.$parent.clearReservationWindow();
			},
			insert(formdata){

				this.formdata=formdata;
			},
			checkForm: function(e){
				e.preventDefault();
				clearInterval(window.mytimer);
				let request=this.formdata;
				request.reservationkey=this.$parent.reservationkey;

				const requestOptions = {
					method: "POST",
					headers: { "Content-Type": "application/json" },
					body: JSON.stringify(request)
				};
				fetch(restdomain+"/api/events/reservation", requestOptions)
					.then(async response => {
						if(response.ok) {

							this.$parent.formdata=this.formdata;
							this.$parent.setReservationkey(null);
							this.$parent.setEventid(-1);
							this.$parent.goToStep("thankyou");
							this.$parent.timer = -1;
							this.$parent.pushToOldEntries(this.formdata);

						}else{
							this.errors.push('Es ist ein Serverfehler aufgetreten');
						}


					})
					.catch((e) => {
						this.errors.push('Ooops. Entschuldig. Unser Terminanfrageserver hat leider ein Problem!<br/> Bitte ' +
							'senden Sie Ihre Anfrage per E-Mail an <a href="mailto:info@fotobachmann.ch">' +
							'info@fotobachmann.ch</a> oder kontaktieren Sie uns unter 052 365 18 11.');
					});
			},
			back(){
				const requestOptions = {
					method: "Delete",
					headers: { "Content-Type": "application/json" },
				};
				fetch(restdomain+"/api/events/reservation/"+this.$parent.eventid+"/"+this.$parent.reservationkey, requestOptions)
					.then()
					.catch((e) => {
						console.log(e);
					});
				this.$parent.setReservationkey(null);
				this.$parent.setEventid(-1);
				this.$parent.goToStep("events");
				this.$parent.timer = -1;
			},
			startIntervalTimer(){
				window.mytimer=window.setInterval(() => {
					this.calculateTimer()
				}, 1000)
			},
			calculateTimer(){
				if(this.$parent.timer>0){
					this.$parent.timer-=1;
					this.timerstep++;
					if(this.timerstep===10){
						this.checkReservationWindow ();
						this.timerstep=0;

					}
				}else{
					clearInterval(window.mytimer);
					this.checkReservationWindow ();
				}
			},
			checkReservationWindow () {
				this.errors = [];
				this.evens = null;
				this.loading = true;

				const requestOptions = {
					method: "POST",
					headers: { "Content-Type": "application/json" },
					body: JSON.stringify({"reservationkey":this.$parent.reservationkey})
				};
				fetch(restdomain+"/api/events/checkreservationwindow", requestOptions)
					.then(async response => {
						if(response.ok) {
							let checkreservationwindow= await response.json();
							console.log(checkreservationwindow);
							if(this.$parent.timer===-1)this.startIntervalTimer();
							this.$parent.timer=checkreservationwindow.remainingseconds;

						} else{
							this.$bvModal.show('modal-prevent-closing');

						}
					})

					.catch((e) => {
						this.errors.push('Es ist ein Render-Serverfehler aufgetreten');
					});
			}
		}
	});

	Vue.component('thankyou', {
		template: '#thankyoubox',
		data() {
			return {
				formdata:{}
			}
		},
		created(){
			this.formdata=this.$parent.formdata;
		},
		methods:{
			showEvents(){
				this.$parent.goToStep("events");
			}
		}

	});

	Vue.component('progress-ring', {
		template: '#progress-ring',
		props: {
			value: {
				type: Number,
				default: 0,
			},
			min: {
				type: Number,
				default: 0,
			},
			max: {
				type: Number,
				default: 1,
			},
			text: {
				type: null,
				default: '',
			},
		},
		computed: {
			theta() {
				const frac = (this.value - this.min) / (this.max - this.min) || 0;
				return frac * 2 * Math.PI;
			},
			path() {
				const large = this.theta > Math.PI;
				return `M0,-46 A46,46,0,${large ? 1 : 0},1,${this.endX},${this.endY}`;
			},
			endX() {
				return Math.cos(this.theta - Math.PI * 0.5) * 46;
			},
			endY() {
				return Math.sin(this.theta - Math.PI * 0.5) * 46;
			},
		},
		filters: {
			totime: function (time) {
				var mins = ~~((time % 3600) / 60);
				var secs = ~~time % 60;
				var ret = "";
				ret += "" + mins + ":" + (secs < 10 ? "0" : "");
				ret += "" + secs;
				return ret;

			}
		},
	});




	var app = new Vue({
		el: "#app",
		data:{
			activView:'events',
			reservationkey: null,
			eventid:-1,
			timer:-1,
			formdata:{},
			oldentries:[],
			bgc:{
				color: 'black'
			}
		},
		created: function() {

		},
		mounted:function(){
			console.log('App mounted!');
			if (localStorage.getItem('caticketactivview')) this.activView = JSON.parse(localStorage.getItem('caticketactivview'));
			if (localStorage.getItem('reservationkey')) this.reservationkey = JSON.parse(localStorage.getItem('reservationkey'));
			if (localStorage.getItem('eventid')) this.eventid = JSON.parse(localStorage.getItem('eventid'));
			if (localStorage.getItem('oldentries')) this.oldentries = JSON.parse(localStorage.getItem('oldentries'));
		},
		methods:{
			goToStep: function(step) {
				this.activView = step;
				localStorage.setItem('caticketactivview', JSON.stringify(this.activView));
			},
			setReservationkey: function(reservationkey){
				this.reservationkey = reservationkey;
				localStorage.setItem('reservationkey', JSON.stringify(this.reservationkey));
			},
			setEventid: function(eventid){
				this.eventid = eventid;
				localStorage.setItem('eventid', JSON.stringify(this.eventid));
			},
			clearReservationWindow: function() {
				this.timer = -1;
				this.setReservationkey(null);
				this.goToStep("events");
			},
			pushToOldEntries: function(formdata){
				if(formdata.ref && formdata.ref===-1){
					formdata.ref=new Date().getTime();
					this.oldentries.push(formdata);
					localStorage.setItem('oldentries', JSON.stringify(this.oldentries));
				}else{
					for (i = 0; i < this.oldentries.length; i++) {
						if(this.oldentries[i].ref===formdata.ref){
							this.oldentries[i]=formdata;
							localStorage.setItem('oldentries', JSON.stringify(this.oldentries));
						}
					}
				}

			}
		}

	});

	/*
	* dashboard
	 */

	Vue.component('login', {
		template: '#loginbox',
		data() {
			return {
				loginformdata:{},
				errors:[]
			}
		},
		created(){

		},
		methods:{
			login: function(e){
				e.preventDefault();

				let request=this.loginformdata;
				errors=[];

				const requestOptions = {
					method: "POST",
					headers: { "Content-Type": "application/json" },
					body: JSON.stringify(request)
				};
				fetch(restdomain+"/api/token", requestOptions)
					.then(async response => {
						if(response.ok) {
							let logininfo= await response.json();
							this.$parent.updateCredentials(logininfo.jwt,logininfo.expiresAt);

						} else{
							this.errors.push('Logindaten falsch');

						}
					})

					.catch((e) => {
						this.errors.push('Logindaten falsch');
					});
			}
		}

	});

	var dashboardApp = new Vue({
		el: "#dashboardapp",
		data:{
			dactivView:'login',
			jwttoken:null,
			jwttokenexpireat:0
		},
		created: function() {

		},
		mounted:function(){
			console.log('App mounted!');
			if (localStorage.getItem('caticketdashboardactivview')) this.dactivView = JSON.parse(localStorage.getItem('caticketdashboardactivview'));
			if (localStorage.getItem('catickjwttoken')) this.jwttoken = localStorage.getItem('catickjwttoken');
			if (localStorage.getItem('catickjwttokenexpireat')) this.jwttokenexpireat = localStorage.getItem('catickjwttokenexpireat');

		},
		methods:{
			goToStep: function(step) {
				this.activView = step;
				localStorage.setItem('caticketdashboardactivview', JSON.stringify(this.dactivView));
			},
			updateCredentials: function(jwttoken,jwttokenexpireat){
				localStorage.setItem('catickjwttoken', jwttoken);
				localStorage.setItem('catickjwttokenexpireat', jwttokenexpireat);
			}
		}

	});

})();