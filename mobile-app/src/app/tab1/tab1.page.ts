import { DatePipe } from '@angular/common';
import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { element } from 'protractor';
import { DriverProvider } from 'protractor/built/driverProviders';
import { ApiService } from '../Service/api.service';

@Component({
  selector: 'app-tab1',
  templateUrl: 'tab1.page.html',
  styleUrls: ['tab1.page.scss'],
})
export class Tab1Page {
  countrycode: string = '91';
  url: string = 'https://wa.me/';
  public counts: any = 0;
  public driver: any;
  public shipments: any[];
  constructor(public api: ApiService, private router: Router, private datePipe: DatePipe) {
    this.shipments = this.api.shipments;
    const param = [];
    this.driver = JSON.parse(localStorage.getItem('userdata')).user_nicename;
    const apikey = localStorage.getItem('apikey');
    this.api.get(apikey).subscribe(
      (data: any) => {
        this.api.loaderhide();
        this.counts = 0;
        this.shipments = this.api.shipments = data;
        // this.getAllShipments();
        this.calculate();

      },
      (error) => {
        this.api.toastMsg('Something went wrong');
        this.api.loaderhide();
      }
    );
  }

  getAllShipments() {
    const apikey = localStorage.getItem('apikey');
    this.api.shipments = [];
    for (let index = 2; index < 11; index++) {
      this.api.get(apikey + '/driver/page/' + index).subscribe(
        (data: any) => {
          this.api.loaderhide();
          this.counts++;

          if (data) {
            this.api.shipments = [
              ...this.api.shipments,
              ...data,
            ];

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
        },

        (error) => {
          this.api.loaderhide();
        }
      );
    }
  }

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

  doRefresh(event) {
    const param = [];
    const apikey = localStorage.getItem('apikey');
    this.api.get(apikey).subscribe(
      (data: any) => {
        this.api.loaderhide();
        this.counts = 0;
        event.target.complete();
        this.api.shipments = this.shipments = data;
        this.calculate();

        // this.getAllShipments();
      },
      (error) => {
        this.api.toastMsg('Something went wrong');
        event.target.complete();
        this.api.loaderhide();
      }
    );
  }

  showShipment(data) {
    this.router.navigateByUrl('shipment?id=' + data.reference_number);
  }

  handleInput(value) {
    const query = value.toLowerCase();
    this.shipments = this.api.shipments.filter((currentValue) => {
      const test1 =
        currentValue.consignee_name.toLowerCase().indexOf(query) > -1;
      const test2 =
        currentValue.consignee_contact.toLowerCase().indexOf(query) > -1;
      const test3 = currentValue.post_title.toLowerCase().indexOf(query) > -1;
      return (
        (test1 || test2 || test3) &&
        currentValue.status != 'DELIVERED' &&
        currentValue.status != 'RETURN TO HUB'
      );
    });
  }
  logout() {
    localStorage.clear();
    this.router.navigateByUrl('login');
  }

  scanner(){
    this.router.navigateByUrl('scanner2');
  }
}
