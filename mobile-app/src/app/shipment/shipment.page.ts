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
    'VECHICLE BREAKDOWN STATUS',
    'LOCATION CHANGE',
    'CUSTOMER REQUEST TO CANCEL',
    'DELIVERED',
    'BAD ADRESS',
    'MOBILE SWITCH OFF',
    'NO RESPONSE',
    'WRONG NUMBER',
    'FUTURE DELIVERY',
    'COD NOT READY',
    'CONSIGNEE NOT AVAILABLE',
    'INVALID NUMBER',
    'BAD ADDRESS'
  ];
  date: Date;
  driver: any;
  countrycode: string = '91';
  url: string = 'https://wa.me/';
  shipper_name: any;
  address: any;
  shipments: any;
  counts: 1;
  constructor(
    private router: Router,
    public api: ApiService,
    private route: ActivatedRoute,
    private datePipe: DatePipe
  ) {
    this.driver = JSON.parse(localStorage.getItem('userdata')).user_nicename;
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


  ngOnInit() {


    if (this.api.shipments == undefined) {
      this.router.navigateByUrl('tabs/pending');
      return;
    }

    this.route.queryParams.subscribe((params) => {
      this.activeShipment = this.api.shipments.filter((currentValue) => {

        console.log(currentValue.reference_number);
        console.log(params.id);

        return currentValue.reference_number == params.id || currentValue.post_title == params.id;
      });



      if (this.activeShipment.length == 0 || this.api.shipments == undefined) {
        this.router.navigateByUrl('tabs/pending');
        return;
      }


      this.id = this.activeShipment[0].ID;
      this.consigneename = this.activeShipment[0].consignee_name;
      this.codAmmount = this.activeShipment[0].cod_amount;
      this.consigneeContact = this.activeShipment[0].consignee_contact;
      this.shipmentPackages = this.activeShipment[0].shipment_packages[0][
        'wpc-pm-description'
      ];
      this.shipmentStatus = this.activeShipment[0].status;
      this.shipmentNumber = this.activeShipment[0].post_title;
      this.shipper_name = this.activeShipment[0].shipper_name;
      this.address = this.activeShipment[0].wpcargo_receiver_address;
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
  onBack() {
    this.router.navigateByUrl("/tabs/pending");
  }
}
