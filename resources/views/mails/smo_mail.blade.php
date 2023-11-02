<?php
    use App\Models\ServiceRequest;
    use App\Models\ServiceRequestFiles;
    use App\Models\ServiceRequestComments;
    use App\Models\SmoServiceRequest;
    use App\Models\DoctorApmtServiceRequest;
    use App\Models\PickupServiceRequest;
    use App\Models\HotelServiceRequest;
    use App\Models\VisaRequest;
    use App\Models\Travellers;
    use App\Models\Patients;
    use App\Models\Patientshospitalid;
    use App\User;
    use App\Models\Doctors;
    $serviceRequest = ServiceRequest::find($service_request_id);
    $patientInformation = Patients::find($serviceRequest->patient_id);
    $serviceWiseData = ServiceRequest::getServiceWiseData('SM', $service_request_id);
    //use DB;
?>

<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title>Health Plus</title>

        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0;">
        <meta name="format-detection" content="telephone=no" />
        <style>
    /* Reset styles */

    body {
      margin: 0;
      padding: 0;
      min-width: 100%;
      width: 100% !important;
      height: 100% !important;
    }

    body,
    table,
    td,
    div,
    p,
    a {
      -webkit-font-smoothing: antialiased;
      text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
      -webkit-text-size-adjust: 100%;
      line-height: 140%;
    }

    table,
    td {
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
      border-collapse: collapse !important;
      border-spacing: 0;
    }

    img {
      border: 0;
      line-height: 100%;
      outline: none;
      text-decoration: none;
      -ms-interpolation-mode: bicubic;
    }

    #outlook a {
      padding: 0;
    }

    .ReadMsgBody {
      width: 100%;
    }
    .ExternalClass {
      width: 100%;
    }
    .basic-table tr td {
      border: 1px solid #E0E0E0;
      padding: 5px 15px;
    }
    h6{
      font-size: 16px;
      margin: 10px 0px 0px;
    }
    .ExternalClass,
    .ExternalClass p,
    .ExternalClass span,
    .ExternalClass font,
    .ExternalClass td,
    .ExternalClass div {
      line-height: 100%;
    }

    /* Rounded corners for advanced mail clients only */

    @media all and (min-width: 600px) {
      .container {
        border-radius: 8px;
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        -khtml-border-radius: 8px;
      }
    }

    /* Set color for auto links (addresses, dates, etc.) */

    a,
    a:hover {
      color: #127DB3;
    }

    .footer a,
    .footer a:hover {
      color: #999999;
    }
  </style>

    </head>

    <body topmargin="0" rightmargin="0" bottommargin="0" leftmargin="0"
        marginwidth="0" marginheight="0" width="100%"
        style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 100%; height: 100%; -webkit-font-smoothing: antialiased; text-size-adjust: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; line-height: 100%;
  background-color: #F0F0F0;
  color: #313131;" bgcolor="#F0F0F0" text="#313131">

        <table width="100%" align="center" border="0" cellpadding="0"
            cellspacing="0"
            style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; width: 100%;"
            class="background">
            <tr>
                <td align="center" valign="top"
                    style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0;"
                    bgcolor="#F0F0F0">

                    <table border="0" cellpadding="0" cellspacing="0"
                        align="center" bgcolor="#FFFFFF" width="700"
                        style="border-collapse: collapse;
                    border-spacing: 0; padding: 0; width: inherit;
                    max-width: 700px;" class="container">
                        <tr>
                            <td align="center" valign="top"
                                background="banner.jpg" bgcolor="#ffffff"
                                style="border-bottom: 1px solid rgb(224, 223, 223);">
                                <table class="col-600" width="100%" height="70"
                                    border="0" align="center" cellpadding="0"
                                    cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td height="15"></td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="line-height: 0px; padding: 0px 15px;vertical-align: top;">
                                                <img
                                                    style="display:inline-block; line-height:0px; font-size:0px; border:0px;"
                                                    src="{{ asset('front/h-plus.png') }}"
                                                    width="120" alt="logo"
                                                    title="logo" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top"
                                style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0 15px; width: 87.5%; font-size: 15px; font-weight: 400; line-height: 160%;
                padding-top: 25px;color: #4f4f4f;font-family: sans-serif;"
                                class="paragraph">
                                <p>This is in reference to SMO Request for Guest
                                    {{ $patientInformation->firstName }} {{
                                    $patientInformation->lastName }}
                                    [C-{{ $patientInformation->id }}].</p>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top"
                                style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0 15px;
                padding-top: 20px;color: #111111;font-family: sans-serif;">
                                <h6>Primary Guest Details:</h6>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top"
                                style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0 15px;
                padding-top: 20px;color: #111111;font-family: sans-serif;font-size: 14px;">
                                <table border="0" cellpadding="0"
                                    cellspacing="0" align="center" width="100%"
                                    class="basic-table">
                                    <tr>
                                        <td width="35%">First Name</td>
                                        <td width="65%">{{
                                            $patientInformation->firstName }}</td>
                                    </tr>
                                    <tr>
                                        <td>Last Name</td>
                                        <td>{{ $patientInformation->lastName }}</td>
                                    </tr>
                                    <tr>
                                        <td>Sex</td>
                                        <td>{{ $patientInformation->sex }}</td>
                                    </tr>
                                    <tr>
                                        <td>Date of Birth</td>
                                        <td>{{ date('d-m-Y',strtotime($patientInformation->dob))
                                            }}</td>
                                    </tr>
                                    <tr>
                                        <td>Country</td>
                                        <td>{{ $patientInformation->country }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top"
                                style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0 15px;
                padding-top: 20px;color: #111111;font-family: sans-serif;">
                                <h6>Referral Details:</h6>
                            </td>
                        </tr>
                        <?php
                            $hospitalinfo = DB::select(DB::raw("SELECT `hospital`.`hospitalTeam`,`hospital_groups`.`title` as `team`,`hospital_groups`.`parent` as `parentid`,(SELECT `hospital_groups`.`title` FROM `hospital_groups` WHERE `hospital_groups`.`id` = `parentid`) as `group` FROM `hospital`,`hospital_groups` WHERE `hospital`.`id` = ".$serviceRequest->hospital_id." AND `hospital`.`hospitalTeam` = `hospital_groups`.`id`"));
                        ?>
                        <tr>
                            <td valign="top"
                                style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0 15px;
                padding-top: 20px;color: #111111;font-family: sans-serif;font-size: 14px;">
                                <table border="0" cellpadding="0"
                                    cellspacing="0" align="center" width="100%"
                                    class="basic-table">
                                    <tr>
                                        <td width="35%">Reffered to Hospital
                                            Group</td>
                                        <td width="65%">{{
                                            @$hospitalinfo[0]->group }}</td>
                                    </tr>
                                    <tr>
                                        <td>Reffered to Hospital Team</td>
                                        <td>{{ @$hospitalinfo[0]->team }}</td>
                                    </tr>
                                    <!-- <tr>
                                    <td>Reffaral Information</td>
                                    <td>{{ @$serviceWiseData[0]->referral_info }}</td>
                                </tr>-->
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top"
                                style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0 15px;
                padding-top: 20px;color: #111111;font-family: sans-serif;">
                                <h6>SMO Enquiry Submitted:</h6>
                            </td>
                        </tr>

                        <tr>
                            <td valign="top"
                                style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0 15px;
                padding-top: 20px;color: #111111;font-family: sans-serif;font-size: 14px;">
                                <table border="0" cellpadding="0"
                                    cellspacing="0" align="center" width="100%"
                                    class="basic-table">
                                    <tr>
                                        <td width="35%">Enquiry Details</td>
                                        <td width="65%">{{
                                            @$serviceWiseData[0]->enquiryDetails
                                            }}</td>
                                    </tr>
                                    <tr>
                                        <td width="35%">Hospital</td>
                                        <td width="65%">{{
                                            @$serviceWiseData[0]->hospitalName
                                            }}</td>
                                    </tr>
                                    <tr>
                                        <td width="35%">Specialty/ Department</td>
                                        <td width="65%">{{
                                            @$serviceWiseData[0]->specality }}</td>
                                    </tr>
                                    <tr>
                                        <td width="35%">Doctor</td>
                                        <td width="65%">{{
                                            @$serviceWiseData[0]->doctorName }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
						<?php if(isset($serviceWiseData[0]->reply) && $serviceWiseData[0]->reply == 1){ ?>
						<tr>
                            <td valign="top"
                                style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0 15px;
                padding-top: 20px;color: #111111;font-family: sans-serif;">
                                <h6>SMO Response from Hospital:</h6>
                            </td>
                        </tr>

                        <tr>
                            <td valign="top"
                                style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0 15px;
                padding-top: 20px;color: #111111;font-family: sans-serif;font-size: 14px;">
                                <table border="0" cellpadding="0"
                                    cellspacing="0" align="center" width="100%"
                                    class="basic-table">
                                    <tr>
                                        <td width="35%">Hospital</td>
                                        <td width="65%">{{
                                            @$serviceWiseData[0]->hospitalName
                                            }}</td>
                                    </tr>
                                    <tr>
                                        <td width="35%">Doctor</td>
                                        <td width="65%">{{
                                            @$serviceWiseData[0]->doctorName
                                            }}</td>
                                    </tr>
                                    <tr>
                                        <td width="35%">Cost Quotation</td>
                                        <td width="65%">{{
                                            @$serviceWiseData[0]->costQuot }}</td>
                                    </tr>
                                    <tr>
                                        <td width="35%">Duration Of Treatment</td>
                                        <td width="65%">{{
                                            @$serviceWiseData[0]->duration_of_treatment }}</td>
                                    </tr>
									<tr>
                                        <td width="35%">Consultents Feedback</td>
                                        <td width="65%">{{
                                            @$serviceWiseData[0]->consultents_feedback }}</td>
                                    </tr>
									<tr>
                                        <td width="35%">General Directives</td>
                                        <td width="65%">{{
                                            @$serviceWiseData[0]->generalDirectives }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
						
						<?php } ?>
						
                        <?php
                        if(!empty($serviceWiseData[0]->files))
                        {
                        echo '<tr>
                            <td valign="top"
                                style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0 15px;
                padding-top: 20px;color: #111111;font-family: sans-serif;">
                                <h6>Attachments:</h6>
                            </td>
                        </tr>';
							
						echo '<tr>';
                        $filesArray = explode(',',$serviceWiseData[0]->files);
                        $div = '<td valign="top"
                            style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0 15px;
            padding-top: 20px;color: #111111;font-family: sans-serif;">';
							
                        $i=1;
                        $div .= '<table border="0" cellpadding="0" cellspacing="0"
                            align="center" width="100%"
                            class="basic-table">';
                            foreach($filesArray as $file){
                            $div.= '<tr><td><a target="_blank"
                                        href="'.url('/').'/'.$file.'"><i
                                            class="fa fa-paperclip"
                                            aria-hidden="true"></i>'.last(explode("/",$file)).'</a></td><td></td></tr>';
                            $i++;
                            }
                            $div.= '</table></td></tr>';
                        echo $div;
                        }
                        ?>
                        <tr>
                            <td valign="top"
                                style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0 15px; width: 87.5%; font-size: 15px; font-weight: 400; line-height: 160%;
                padding-top: 25px;color: #4f4f4f;font-family: sans-serif;"
                                class="paragraph">
                                <p>Click for to <a
                                        href="https://hplus-bd.com/service-requests/view?request_id={{ $service_request_id }}&service_id={{ $serviceRequest->service_id }}">SM-{{
                                        $service_request_id }}</a> view details</p>
                                <p>Best Regards</p>
                                <p>Healthplus Support Team</p>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" valign="top"
                                background="banner.jpg" bgcolor="#CBF0D1">
                                <table class="col-600" width="100%" height="70"
                                    border="0" align="center" cellpadding="0"
                                    cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td height="15"></td>
                                        </tr>
                                        <tr>
                                            <td width="4%"
                                                style="line-height: 0px; padding: 0px 0px 0px 15px;vertical-align: top;">
                                                <img
                                                    style="display:inline-block; line-height:0px; font-size:0px; border:0px;"
                                                    src="{{ asset('front/h-plus-icon.png') }}"
                                                    width="80" alt="logo">
                                            </td>
                                            <td width="46%"
                                                style="line-height: 0px; padding: 0px 15px;vertical-align: top;font-family: sans-serif;font-size: 10px;font-weight:600;">
                                                <p style="margin: 0px;">4
                                                    Mohakhali C/A, Bir Uttam AK
                                                    Khandakar Road 1212 Dhaka,
                                                    Dhaka Division, Bangladesh.</p>
                                                <p style="margin-bottom: 0px;">Email:<br />
                                                    Phone:</p>
                                            </td>
                                            <td width="25%"></td>
                                            <td width="25%"
                                                style="line-height: 0px; padding: 0px 15px;vertical-align: top;text-align: center;font-family: sans-serif;font-size: 10px;font-weight:600;">
                                                <p style="margin: 0px;">Social
                                                    Links</p>
                                                <a href="#"
                                                    style="display: inline-block;padding: 3px 5px;"><img
                                                        style="display:inline-block; line-height:0px; font-size:0px; border:0px;"
                                                        src="{{ asset('front/facebook.png') }}"
                                                        alt="logo"></a>
                                                <a href="#"
                                                    style="display: inline-block;padding: 3px 5px;"><img
                                                        style="display:inline-block; line-height:0px; font-size:0px; border:0px;"
                                                        src="{{ asset('front/linkedin.png') }}"
                                                        alt="logo"></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="15"></td>
                                        </tr>
                                    </tbody></table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>

</html>