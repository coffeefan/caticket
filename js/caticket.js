
( function() { 
	Vue.component('eventbox', {
		data () {
			return {
			  loading: false,
			  events: [],
			  error: null
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
			fetchData () {
				this.error = null;
				this.evens = null;
			  	this.loading = true;
				
				const requestOptions = {
					method: "GET",
					headers: { "Content-Type": "application/json" },
					body: JSON.stringify(this.$parent.formdata)
				};
				fetch("/api/events/active", requestOptions)
				.then(response => { 
					if(response.ok){
						return response.json()    
					} else{
						this.errors.push('Es ist ein Serverfehler aufgetraten');
					}                
				})
				.then(response => {
					this.events = response; 
				})
				.catch((e) => {
					this.errors.push('Es ist ein Render-Serverfehler aufgetraten');
				});
			}
		  }
		}
	);
	
	Vue.component('register', {
		data() {
		  return { checked: false, title: 'register' }
		},
		methods: {
		  check() { this.checked = !this.checked; this.title="saleti"; }
		}
	  });  
/*
	var fbTerminStepOne = Vue.component('fb-TerminStepOne',{
		template: '\
			<div class="fb-TerminStepOne">\
				<div class="container">\
					<div class="row">\
						<div class="col-md-7">\
							<div class="container">\
								<div class="row">\
									<div class="col-sm-10">\
										<h2 class="fbterminTitle">{{atts.stepone.title}}</h2>\
										<div v-if="errors.length" class="errors">\
											<div v-for="error in errors">- {{ error }} </div>\
										  </div>\
										<select v-model="formdata.organisationsart"  class="form-control" >\
										<option value="undefined" selected disabled>Organisationsart wählen</option>\
										<option>Schule</option>\
										<option>Kindergarten</option>\
										<option>Spielgruppe</option>\
										<option>Kinderkrippe</option>\
										</select>\
									</div>\
								</div>\
								<div class="row">\
									<div class="col-md-12 fbTerminTextLabel">{{atts.stepone.typ}}</div>\
									<div class="col-md-12 fbTerminTextInfo">{{atts.stepone.typtext}}</div>\
								</div>\
								<div class="row typselectionboxes">\
									<div class="col-md-4">\
										<button class="typselectionbox" v-on:click="submitOne(atts.stepone.typonevalue)" >\
											<svg   viewBox="0 0 16 16" class="bi bi-display-fill typselectionbox" fill="currentColor" xmlns="http://www.w3.org/2000/svg">\
												<path d="M6 12c0 .667-.083 1.167-.25 1.5H5a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1h-.75c-.167-.333-.25-.833-.25-1.5h4c2 0 2-2 2-2V4c0-2-2-2-2-2H2C0 2 0 4 0 4v6c0 2 2 2 2 2h4z"/>\
											</svg>\
										</button><br/>\
										{{atts.stepone.typonevalue}}\
									</div>\
									<div class="col-sm-4">\
										<button class="typselectionbox" v-on:click="submitOne(atts.stepone.typtwovalue)" >\
											<svg   viewBox="0 0 16 16" class="bi bi-display-fill typselectionbox" fill="currentColor" xmlns="http://www.w3.org/2000/svg">\
												<path fill-rule="evenodd" d="M1 2.828v9.923c.918-.35 2.107-.692 3.287-.81 1.094-.111 2.278-.039 3.213.492V2.687c-.654-.689-1.782-.886-3.112-.752-1.234.124-2.503.523-3.388.893zm7.5-.141v9.746c.935-.53 2.12-.603 3.213-.493 1.18.12 2.37.461 3.287.811V2.828c-.885-.37-2.154-.769-3.388-.893-1.33-.134-2.458.063-3.112.752zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>>\
											</svg>\
										</button><br/>\
										{{atts.stepone.typtwovalue}}\
									</div>\
									<div class="col-sm-4">\
										<button class="typselectionbox" v-on:click="submitOne(atts.stepone.typthreevalue)" >\
											<svg   viewBox="0 0 16 16" class="bi bi-display-fill typselectionbox" fill="currentColor" xmlns="http://www.w3.org/2000/svg">\
												 <path fill-rule="evenodd" d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>\n\
											</svg>\
										</button><br/>\
										{{atts.stepone.typthreevalue}}\
									</div>\
								</div>\
							</div>\
						</div>\
					</div>\
				</div>\
			</div>',
		props: ['atts','formdata'],
		data: function() { return {
			TypeOneValue: 'OnlineVariante',
			TypeTwoValue: 'Klassiche Fotomappe',
			TypeThreeValue: 'Nur Gruppenfoto',
			errors: []
		} },
		methods: {
			submitOne: function(typselect) {
				if(!this.manuelcheck()) return false;
				this.$parent.formdata.type=typselect;
				this.$parent.goToStep(2);
			},

			manuelcheck: function(){
				this.errors = [];
				if (!this.$parent.formdata.organisationsart) {
					this.errors.push('Bitte wählen Sie eine Organisationsart aus.');
				}

				return (this.errors.length<1);
			}

		}
	});

	var fbTerminStepTwo = Vue.component('fb-TerminStepTwo',{
		template: '<div class="fb-TerminStepTwo">\
				<div class="container">\
					<div class="row">\
						<div class="col-md-12">\
							<h2 class="fbterminTitle">{{atts.steptwo.title}}</h2>\
							<div v-if="errors.length" class="errors">\
								<div v-for="error in errors">- {{ error }} </div>\
							</div>\
						</div>\
					</div>\
					<form @submit="checkForm" id="addressform">\
						<div class="form-row">\
							<div class="form-group col-md-6">\
								<label for="inputIinstitution">Name von {{formdata.organisationsart}}*</label>\
								<input type="text" class="form-control" v-model="formdata.institution" required \>\
							</div>\
							\
						</div>\
						<div class="form-row">\
							<div class="form-group col-md-6">\
								<label for="inputStrasse">Strasse*</label>\
								<input type="text" class="form-control" id="inputStrasse"  v-model="formdata.street" required \>\
							</div>\
							<div class="form-group col-md-6">\
								<label for="inputCity">PLZ / Ort*</label>\
								<input type="text" class="form-control" id="inputCity"  v-model="formdata.city" required\>\
							</div>\
						</div>\
						<div class="form-row">\
							<div class="form-group col-md-6">\
								<label for="inputFirstname">Vorname*</label>\
								<input type="text" class="form-control" id="inputFirstname"  v-model="formdata.firstname" required\>\
							</div>\
							<div class="form-group col-md-6">\
								<label for="inputFirstname">Nachname*</label>\
								<input type="text" class="form-control" id="inputFirstname"  v-model="formdata.lastname" required\>\
							</div>\
						</div>\
						<div class="form-row">\
							<div class="form-group col-md-6">\
								<label for="inputEmail">E-Mail*</label>\
								<input type="email" class="form-control" id="inputEmail"  v-model="formdata.email" required \>\
							</div>\
							<div class="form-group col-md-6">\
								<label for="inputPhone">Telefonnummer*</label>\
								<input type="text" class="form-control" id="inputPhone"  v-model="formdata.phone" required \>\
							</div>\
						</div>\
						<div class="form-row">\
							<div class="form-group col-md-12 text-right">\
								<button class="back" type="button" v-on:click="back()" >Zurück</button>\
								<button type="submit" class="next">Weiter</button>\
							</div>\
						</div>\
					</form>\
				</div>\
			</div>',
		props: ['atts','formdata'],
		data:  function() { return {
			errors:[]
		} },
		methods: {
			back(){
				this.$parent.gobackToStep(1);
			},
			checkForm: function(e){
				e.preventDefault();
				this.$parent.goToStep(3);

			}
		}
	});

	var fbTerminStepThree = Vue.component('fb-TerminStepThree',{
		template: '<div class="fb-TerminStepThree">\
				<div class="container">\
					<div class="row">\
						<div class="col-md-12">\
							<h2 class="fbterminTitle">{{atts.stepthree.title}}</h2>\
							<div v-if="errors.length" class="errors">\
								<div v-for="error in errors"><span v-html="error"> </span> </div>\
							</div>\
						</div>\
					</div>\
					<form @submit="checkForm" id="addressform">\
						<div class="form-row">\
							<div class="form-group col-md-4">\
								<label for="wishdateOne-datepicker" >Wunschdatum Prio 1 *</label>\
								<b-form-datepicker id="wishdateOne-datepicker" v-model="formdata.wishdateOne" \
								v-bind="labels[locale] || {}"\:locale="locale" :start-weekday="weekday" :show-decade-nav="showDecadeNav"\
      							:hide-header="hideHeader" class="mb-2 wishdate"></b-form-datepicker>\
							</div>\
							<div class="form-group col-md-4">\
								<label for="wishdateTwo-datepicker" >Wunschdatum Prio 2</label>\
								<b-form-datepicker id="wishdateTwo-datepicker" v-model="formdata.wishdateTwo"\
								v-bind="labels[locale] || {}"\:locale="locale" :start-weekday="weekday" :show-decade-nav="showDecadeNav"\
      							:hide-header="hideHeader" class="mb-2 wishdate"></b-form-datepicker>\
							</div>\
							<div class="form-group col-md-4">\
								<label for="wishdateThree-datepicker" >Wunschdatum Prio 3</label>\
								<b-form-datepicker id="wishdateThree-datepicker" v-model="formdata.wishdateThree" \
								v-bind="labels[locale] || {}"\:locale="locale" :start-weekday="weekday" :show-decade-nav="showDecadeNav"\
      							:hide-header="hideHeader" class="mb-2 wishdate"></b-form-datepicker>\
							</div>\
						</div>\
						<div class="form-row">\
							<div class="form-group col-md-12 text-right">\
								<button class="back" type="button" v-on:click="back()" >Zurück</button>\
								<button type="submit" class="next">Anfrage senden</button>\
							</div>\
						</div>\
					</form>\
				</div>\
			</div>',
		props: ['atts','formdata'],
		data:  function() { return {
			errors:[],
			value: '',
			locale: 'de',
			showDecadeNav: false,
			hideHeader: false,
			locales: [
				{ value: 'en-US', text: 'English US (en-US)' },
				{ value: 'de', text: 'German (de)' },
			],
			weekday: 0,
			weekdays: [
				{ value: 0, text: 'Sunday' },
				{ value: 1, text: 'Monday' },
				{ value: 6, text: 'Saturday' }
			],
			labels: {
				de: {
					labelPrevDecade: 'Vorheriges Jahrzehnt',
					labelPrevYear: 'Vorheriges Jahr',
					labelPrevMonth: 'Vorheriger Monat',
					labelCurrentMonth: 'Aktueller Monat',
					labelNextMonth: 'Nächster Monat',
					labelNextYear: 'Nächstes Jahr',
					labelNextDecade: 'Nächstes Jahrzehnt',
					labelToday: 'Heute',
					labelSelected: 'Ausgewähltes Datum',
					labelNoDateSelected: 'Kein Datum gewählt',
					labelCalendar: 'Kalender',
					labelNav: 'Kalendernavigation',
					labelHelp: 'Mit den Pfeiltasten durch den Kalender navigieren'
				}
			}
		} },
		methods: {
			back(){
				this.$parent.gobackToStep(2);
			},
			validateForm(){
				this.errors = [];
				if (!this.$parent.formdata.wishdateOne) {
					this.errors.push('Bitte wählen Sie mindestens Wunschdatum 1 Prio aus.');
				}
				return (this.errors.length<1);
			},
			checkForm: function(e){
				e.preventDefault();
				if(!this.validateForm()) return;
				var queryString = '?action=fb-send';


				const requestOptions = {
					method: "POST",
					headers: { "Content-Type": "application/json" },
					body: JSON.stringify(this.$parent.formdata)
				};
				fetch(window.ajaxurl + queryString, requestOptions)
					.then()
					.then(()=>{
						this.$parent.goToStep(4);
					})
					.catch((e) => {
						this.errors.push('Ooops. Entschuldig. Unser Terminanfrageserver hat leider ein Problem!<br/> Bitte ' +
							'senden Sie Ihre Anfrage per E-Mail an <a href="mailto:info@fotobachmann.ch">' +
							'info@fotobachmann.ch</a> oder kontaktieren Sie uns unter 052 365 18 11.');
					});
			}
		}
	});

	var fbTerminStepTfour = Vue.component('fb-TerminStepFour',{
		template: '<div class="fb-TerminStepFour">\
				<div class="container">\
					<div class="row">\
						<div class="col-md-12">\
							<h2 class="fbterminTitle">{{atts.stepfour.title}}</h2>\
							<p v-html="atts.stepfour.text"></p>\
							<button type="button" class="nextstartseite" v-on:click="toStartpage()">Weiter zur Startseite</button>\
						</div>\
					</div>\
				</div>\
			</div>',
		props: ['atts','formdata'],
		data:  function() { return {
			errors:[]
		} },
		methods: {
			toStartpage(){
				window.location.replace("http://www.fotobachmann.ch");
			}
		}
	});*/

	 // boot up the demo
	 Vue.config.devtools = true
	 var demo = new Vue({
		el: "#app",
		data:{
			actview:'events',
		},
		created: function() {
			this.actview='events';
		},
		
      });

/*

	var elements = document.querySelectorAll('[data-fb-atts]');
	elements.forEach( function( element ) {
		var atts = JSON.parse( element.getAttribute('data-fb-atts') )
		var vm = new Vue({
			el: element,
			data: {
				currentStep:1,
				formdata:{}
			},
			template: '<div class="fb-container">\
			\<fb-TerminStepOne :atts="atts" :formdata="formdata" v-if="currentStep == 1"/>\
			\<fb-TerminStepTwo :atts="atts" :formdata="formdata" v-if="currentStep == 2" />\
			\<fb-TerminStepThree :atts="atts" :formdata="formdata" v-if="currentStep == 3" />\
			\<fb-TerminStepFour :atts="atts" :formdata="formdata" v-if="currentStep == 4" />\
			</div>',
			created: function() {
				this.atts = atts;
				console.log(atts);
			},
			mounted:function(){
				console.log('App mounted!');

				if (localStorage.getItem('fbterminformdata')) this.formdata = JSON.parse(localStorage.getItem('fbterminformdata'));
				if (localStorage.getItem('fbtermincurrentStep')) this.currentStep = JSON.parse(localStorage.getItem('fbtermincurrentStep'));
			},
			methods:{
				goToStep: function(step) {
					this.currentStep = step;

					if(this.currentStep===4){
						localStorage.clear();
						localStorage.removeItem('fbtermincurrentStep');
						localStorage.removeItem('fbterminformdata');
					}else{
						localStorage.setItem('fbtermincurrentStep', JSON.stringify(this.currentStep));
						localStorage.setItem('fbterminformdata', JSON.stringify(this.formdata));
					}
				},
				gobackToStep: function(step) {
					this.currentStep = step;
					localStorage.setItem('fbtermincurrentStep', JSON.stringify(this.currentStep));
				}
			}
		} );
	});*/

})();