import { Component } from '@angular/core';
import { ApiService } from '../Service/api.service';

@Component({
  selector: 'app-tabs',
  templateUrl: 'tabs.page.html',
  styleUrls: ['tabs.page.scss']
})
export class TabsPage {


  constructor(public api: ApiService) {
    const param = [];
    const apikey = localStorage.getItem('apikey');
    this.api.get(apikey ).subscribe((data: any) => {
      this.api.loaderhide();
      this.api.shipments = data;
      // this.getAllShipments();
    }, error => {
      this.api.toastMsg('Something went wrong');
      this.api.loaderhide();
    });

  }


  getAllShipments() {
    const apikey = localStorage.getItem('apikey');
    this.api.shipments = [];
    for (let index = 2; index < 11; index++) {
      this.api.get(apikey + '/driver/page/' + index).subscribe((data: any) => {
        this.api.loaderhide();
        if (data) {
          this.api.shipments = [...this.api.shipments, ...data];
        }
        // this.api.toastMsg('Something went wrong');
        this.api.loaderhide();
      });
    }
  }


}
