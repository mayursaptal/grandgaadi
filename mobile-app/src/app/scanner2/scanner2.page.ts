import { Component, OnInit } from '@angular/core';
import { BarcodeScanner } from '@ionic-native/barcode-scanner/ngx';
import { Router } from '@angular/router';
import { ApiService } from '../Service/api.service';

@Component({
  selector: 'app-scanner2',
  templateUrl: './scanner2.page.html',
  styleUrls: ['./scanner2.page.scss'],
  providers: [BarcodeScanner]
})
export class Scanner2Page implements OnInit {

  constructor(private barcodeScanner: BarcodeScanner , private router: Router ,  public api: ApiService,) { }


  ngOnInit() {
    
    var comp = this;
    this.barcodeScanner.scan().then(barcodeData => {
      console.log('Barcode data', barcodeData);
      comp.router.navigateByUrl('shipment?id=' + barcodeData.text);
      comp.api.toastMsg(barcodeData.text);
    }).catch(err => {
      console.log('Error', err);
      comp.api.toastMsg(err);
    });
  }

}
