function timeCtrl() {
  this.hmstyle={
    'white-space': "nowrap",
  }
  this.updateHours = function() {
    if (isNaN(this.ngModel.hours) || this.ngModel.hours.length>2) {
      this.ngModel.hours="";
    }
    if (this.ngModel.hours<0) {
      this.ngModel.hours=0;
    }
    if (this.ngModel.hours>23) {
      this.ngModel.hours=23;
    }
  }

  this.setMinutes = function() {
    if (!this.ngModel.minutes) {
      this.ngModel.minutes="00";
    } else if (this.ngModel.minutes.length==1) {
      this.ngModel.minutes="0"+this.ngModel.minutes;
    }
  }

  this.updateMinutes = function() {
    if (isNaN(this.ngModel.minutes) || !this.ngModel.minutes || this.ngModel.minutes.length>2) {
      this.ngModel.minutes="";
    }
    if (this.ngModel.minutes<0) {
      this.ngModel.minutes=0;
    }
    if (this.ngModel.minutes>59) {
      this.ngModel.minutes=59;
      this.ngModel.minutes="59";
    }
  }
}

angular.module('dsrcommon.utilities.dsrtime',[]).
  component('dsrtime',{
    replace:true,
    template:
    '<span ng-style="$ctrl.hmstyle"><input type="text" pattern="[0-9][0-9]?" size="2" ng-model="$ctrl.ngModel.hours" ng-change="$ctrl.updateHours()">:<input type="text" size="2" ng-model="$ctrl.ngModel.minutes" ng-change="$ctrl.updateMinutes()" ng-blur="$ctrl.setMinutes()"></span>',
    bindings: {
      ngModel: "=",
      onUpdate: '&'
    },
    controller:timeCtrl
  }
);
