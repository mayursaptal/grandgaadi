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

  public driver: any;
  public shipments: any[];
  constructor(public api: ApiService, private router: Router) {
    this.shipments = this.api.shipments;
    const param = [];
    this.driver = JSON.parse(localStorage.getItem('userdata')).user_nicename;
    const apikey = localStorage.getItem('apikey');
    this.api.get(apikey + '/driver').subscribe(
      (data: any) => {
        this.api.loaderhide();
        this.shipments = this.api.shipments = data;
        this.getAllShipments();
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
          if (data) {
            this.api.shipments = this.shipments = [
              ...this.api.shipments,
              ...data,
            ];
          }
        },
        (error) => {
          // this.api.toastMsg('Something went wrong');
          this.api.loaderhide();
        }
      );
    }
  }

  doRefresh(event) {
    const param = [];
    const apikey = localStorage.getItem('apikey');
    this.api.get(apikey + '/driver').subscribe(
      (data: any) => {
        this.api.loaderhide();
        event.target.complete();
        this.api.shipments = this.shipments = data;
        this.getAllShipments();
      },
      (error) => {
        this.api.toastMsg('Something went wrong');
        event.target.complete();
        this.api.loaderhide();
      }
    );
  }

  showShipment(data) {
    this.router.navigateByUrl('shipment?id=' + data.ID);
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
}
