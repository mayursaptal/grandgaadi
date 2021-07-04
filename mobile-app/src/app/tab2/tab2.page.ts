import { DatePipe } from '@angular/common';
import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from '../Service/api.service';

@Component({
  selector: 'app-tab2',
  templateUrl: 'tab2.page.html',
  styleUrls: ['tab2.page.scss']
})
export class Tab2Page {
  public codAmmount: any;
  public shipments: any[];
  constructor(public api: ApiService, private router: Router , private datePipe: DatePipe) {
    this.shipments = this.api.shipments;
    this.calculate();
  }


  isToday(date) {
    const today = new Date();
    return date.getDate() === today.getDate() &&
      date.getMonth() === today.getMonth() &&
      date.getFullYear() === today.getFullYear();
  }
  // calculate() {
  //   this.codAmmount = 0;
  //   if (this.shipments) {
  //     this.shipments.forEach(element => {
  //       try {
  //         const date = new Date(element.shipment_history[element.shipment_history.length - 1].date);
  //         if (element.status === 'DELIVERED' && this.isToday(date)) {
  //           this.codAmmount = parseFloat(this.codAmmount) + parseFloat(element.cod_amount);
  //         }
  //       } catch (error) {
  //         const date = new Date();
  //         if (element.status === 'DELIVERED' && this.isToday(date)) {
  //           this.codAmmount = parseFloat(this.codAmmount) + parseFloat(element.cod_amount);
  //         }
  //       }
  //     });
  //   }
  // }

  calculate() {
    this.api.shipments = this.api.shipments.filter((shipment) => {
      let history = shipment.shipment_history;

      let flag = false;
      var d = new Date();
      var date = this.datePipe.transform(d, 'yyyy-MM-dd');

      for (let index = 0; index < history.length; index++) {
        const element = history[index];
        if (element.status === "OUT FOR DELIVERY") {
          if (element.date === date) {
            flag = true;
          }
        }
      }
      return flag;
    });

    this.shipments = this.api.shipments;
  }
  getAllShipments() {
    const apikey = localStorage.getItem('apikey');
    this.api.shipments = [];
    for (let index = 2; index < 11; index++) {
      this.api.get(apikey + '/driver/page/' + index).subscribe((data: any) => {
        this.api.loaderhide();
        if (data) {
          this.api.shipments = this.shipments = [...this.api.shipments, ...data];
        }
        this.calculate();
      }, error => {
        // this.api.toastMsg('Something went wrong');
        this.api.loaderhide();
      });
    }
  }

  doRefresh(event) {
    const param = [];
    const apikey = localStorage.getItem('apikey');
    this.api.get(apikey ).subscribe((data: any) => {
      this.api.loaderhide();
      event.target.complete();
      this.api.shipments = this.shipments = data;
      // this.getAllShipments();
      this.calculate();
    }, error => {
      this.api.toastMsg('Something went wrong');
      event.target.complete();
      this.api.loaderhide();
    });
  }
  handleInput(value) {
    const query = value.toLowerCase();
    console.log(this.api.shipments);
    this.shipments = this.api.shipments.filter((currentValue) => {
      const test1 = currentValue.consignee_name.toLowerCase().indexOf(query) > -1;
      const test2 = currentValue.consignee_contact.toLowerCase().indexOf(query) > -1;
      const test3 = currentValue.post_title.toLowerCase().indexOf(query) > -1;
      return (test1 || test2 || test3) && (currentValue.status == 'DELIVERED');
    });
  }
  logout() {
    localStorage.clear();
    this.router.navigateByUrl('login');
  }
}
