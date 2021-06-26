import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { count } from 'node:console';
import { ApiService } from '../Service/api.service';

@Component({
  selector: 'app-tab4',
  templateUrl: './tab4.page.html',
  styleUrls: ['./tab4.page.scss'],
})
export class Tab4Page {
  countrycode: string = '91';
  url: string = 'https://wa.me/';

  public driver: any;
  public codCount: any;
  public deliveredCount: any;
  public pendingCount: any;
  public shipments: any[];
  public codAmount: any;
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

  async getAllShipments() {
    const apikey = localStorage.getItem('apikey');
    this.api.shipments = [];
    for (let index = 2; index < 11; index++) {
      await this.api.get(apikey + '/driver/page/' + index).subscribe(
        (data: any) => {
          this.api.loaderhide();
          if (data) {
            this.api.shipments = this.shipments = [
              ...this.api.shipments,
              ...data,
            ];
            // console.log(this.api.shipments);


            this.codCount = 0.0;
            this.deliveredCount = 0;
            this.pendingCount = 0;
            this.deliveredCount = this.api.shipments.filter(x => x.status === 'DELIVERED').length;
            this.pendingCount = this.api.shipments.filter(x => x.status != 'DELIVERED').length;
            for (let index = 0; index < this.api.shipments.length; index++) {
              const element = this.api.shipments[index];
              if (element.status === 'DELIVERED') {
                if (element.cod_amount) {
                  let cod = element.cod_amount;

                  this.codCount += parseFloat(cod);
                  this.codAmount = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'AED' }).format(this.codCount)
                }
              }


            }
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
  onPending() {
    this.router.navigateByUrl("/tabs/tab1")
  }
  onDeliver() {
    this.router.navigateByUrl("/tabs/tab2")
  }
}
