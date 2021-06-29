import { DatePipe } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from '../Service/api.service';
// import {Pod} from '../pod/pod.module';
@Component({
  selector: 'app-shipment',
  templateUrl: './shipment.page.html',
  styleUrls: ['./shipment.page.scss'],
})
export class ShipmentPage implements OnInit {
  public id: any;
  public codAmmount: any;
  public consigneeContact: any;
  public shipmentNumber: any;
  public consigneename: any;
  public referenceNumber: any;
  public shipmentPackages: any;
  public activeShipment: any;
  public shipmentStatus: any;
  public remark: any;
  public status: any[] = [
    'MISROUTE',
    'CUSTOMER REQUEST TO CANCEL',
    'DELIVERED',
    'BAD ADRESS',
    'MOBILE SWITCH OFF',
    'NO RESPONSE',
    'WRONG NUMBER',
    'FUTURE DELIVERY',
    'COD NOT READY',
    'CONSIGNEE NOT AVAILABLE',
    'Invalid number',
    'Bad address', 
    'COD not ready'
  ];
  date: Date;
  driver: any;

  constructor(
    private router: Router,
    public api: ApiService,
    private route: ActivatedRoute,
    private datePipe: DatePipe
  ) {
    this.driver = JSON.parse(localStorage.getItem('userdata')).user_nicename;
  }

  ngOnInit() {
    this.route.queryParams.subscribe((params) => {
      this.activeShipment = this.api.shipments.filter((currentValue) => {
        return currentValue.ID == params.id;
      });
      this.id = this.activeShipment[0].ID;
      this.consigneename = this.activeShipment[0].consignee_name;
      this.codAmmount = this.activeShipment[0].cod_amount;
      this.consigneeContact = this.activeShipment[0].consignee_contact;
      this.shipmentPackages = this.activeShipment[0].shipment_packages[0][
        'wpc-pm-description'
      ];
      this.shipmentStatus = this.activeShipment[0].status;
      this.shipmentNumber = this.activeShipment[0].post_title;
    });
  }

  onSave() {
    if (!this.shipmentStatus) {
      this.api.toastMsg('Please select shipment status');
      return;
    }
    const history = [
      {
        date: this.datePipe.transform(new Date(), 'yyyy-MM-dd'),
        time: this.datePipe.transform(new Date(), 'HH:mm'),
        status: this.shipmentStatus,
        remarks: this.remark,
        'updated-name': '',
      },
    ];

 
    this.activeShipment[0].shipment_history.push(history);
    const param = {
      shipment: this.shipmentNumber,
      wpcargo_status: this.shipmentStatus,
      shipment_history: history,
    };

    const apikey = localStorage.getItem('apikey');
    this.api.post(apikey + '/shipment/update', param).subscribe(
      (data: any) => {
        this.api.loaderhide();

        if (this.shipmentStatus === 'DELIVERED') {
          this.router.navigateByUrl('pod?id=' + this.shipmentNumber);
          this.api.toastMsg('Updated Successfully');
        }
      },
      (error) => {
        this.api.toastMsg('Something went wrong');
        this.api.loaderhide();
      }
    );
  }
  onBack(){
    this.router.navigateByUrl("/tabs/pending");
  }
}
