window.onload=function(){
    var OwnSelectedSeat = new Array(3);
    var OwnSelectedSeatCount = 0;
    OwnSelectedSeatinitialize();

    function OwnSelectedSeatinitialize() {
        OwnSelectedSeat = new Array(0);
        OwnSelectedSeat = new Array(3);
        OwnSelectedSeat[0] = new Array(5);
        OwnSelectedSeat[1] = new Array(5);
        OwnSelectedSeat[2] = new Array(5);
        OwnSelectedSeat[3] = new Array(5);
        OwnSelectedSeat[4] = new Array(5);
        OwnSelectedSeatCount = 0;
    }
    var SeatsInfoT = null
    var SeatsLockInfoT = null
    function setupSeat(sSeatInfot, sSeatInfoLockt) {
        $("#yaSelectSeat").html("<div class='SeatTips'> <span class='fl'><img src='/style/img/Seat_null.gif' />可选座位</span><span class='fl'><img src='/style/img/Seat_sold.gif' />已售出座位</span><span class='fl'><img src='/style/img/Seat_lovers.gif' />情侣座</span><span class='fl'><img src='/style/img/Seat_own.gif' />您选择的座位</span> <span class='fr' id='Seats_CountNum'>本场次共有：个座位</span></div><div class='Fast' id='div_SelectSeat'><p class='Screen'><img src='/style/img/Screen.jpg' /></p><table id='table_SelectSeat' width='612' class='ScreenTable' border='0' cellspacing='0' cellpadding='0' align='center'></table><table width='612' class='ScreenTable' border='0' cellspacing='0' cellpadding='0' align='center'><tr><td><input name='btn_Bookingseat' id='btn_Bookingseat' type='button' disabled value='完成订座' onclick='bookingseated();' /></td></tr></table></div>");

        SeatsInfoT = CreateSeatMap(sSeatInfot, sSeatInfoLockt, 'SelectSeat', '2');
        $("#Seats_CountNum").html("本场次共有：" + SeatsInfoT.iSeats + "个座位");

    }
    sAllSeatInfo = '{"Rows":"9","Cells":"14","iSeats":"102","iInvalidSeats":"24","SeatInfo":[{"Row":"1","Name":"1","Cell":"11,10,09,08,07,06,05,04,03,0,0,0,02,01","SeatNo":",,,,,,,,,0,0,0,,","SeatPieceNo":"001,01,01,01,01,01,01,01,01,0,0,0,01,01"},{"Row":"2","Name":"2","Cell":"11,10,09,08,07,06,05,04,03,0,0,0,02,01","SeatNo":",,,,,,,,,0,0,0,,","SeatPieceNo":"001,01,01,01,01,01,01,01,01,0,0,0,01,01"},{"Row":"3","Name":"3","Cell":"11,10,09,08,07,06,05,04,03,0,0,0,02,01","SeatNo":",,,,,,,,,0,0,0,,","SeatPieceNo":"001,01,01,01,01,01,01,01,01,0,0,0,01,01"},{"Row":"4","Name":"4","Cell":"11,10,09,08,07,06,05,04,03,0,0,0,02,01","SeatNo":",,,,,,,,,0,0,0,,","SeatPieceNo":"001,01,01,01,01,01,01,01,01,0,0,0,01,01"},{"Row":"5","Name":"5","Cell":"11,10,09,08,07,06,05,04,03,0,0,0,02,01","SeatNo":",,,,,,,,,0,0,0,,","SeatPieceNo":"001,01,01,01,01,01,01,01,01,0,0,0,01,01"},{"Row":"6","Name":"6","Cell":"11,10,09,08,07,06,05,04,03,0,0,0,02,01","SeatNo":",,,,,,,,,0,0,0,,","SeatPieceNo":"001,01,01,01,01,01,01,01,01,0,0,0,01,01"},{"Row":"7","Name":"7","Cell":"11,10,09,08,07,06,05,04,03,0,0,0,02,01","SeatNo":",,,,,,,,,0,0,0,,","SeatPieceNo":"001,01,01,01,01,01,01,01,01,0,0,0,01,01"},{"Row":"8","Name":"8","Cell":"11,10,09,08,07,06,05,04,03,0,0,0,02,01","SeatNo":",,,,,,,,,0,0,0,,","SeatPieceNo":"001,01,01,01,01,01,01,01,01,0,0,0,01,01"},{"Row":"9","Name":"9","Cell":"14,13,12,11,10,09,08,07,06,05,04,03,02,01","SeatNo":",,,,,,,,,,,,,","SeatPieceNo":"001,01,01,01,01,01,01,01,01,01,01,01,01,01"}]}';
    sAllLockSeatInfo = '{"SeatInfo":[{"Row":"6","Cell":"05"}]}';
    setupSeat(sAllSeatInfo, sAllLockSeatInfo);
    function bookingseated() {
        $("#yaSelectSeat").removeClass("Fast");
        $("#yaSelectSeat").css("display", "none");
        $("#Seat_Select").css("display", "");

        $("#yaUserInfo").removeClass("Fast");
        
        $("#yaUserInfo").css("display", "");
        $("#sPhone").focus();
        
        setProcessClass(5);
       
    }
    function addSeat(Seatobj) {		

        if (Seatobj.bIsSeatLock == 1) {
            alert("座位已被锁定，无法为您选中。");
            return false;
        }
        if ($(Seatobj).attr("bIsOwn") == 0) {
            if (IsAddSeat(Seatobj) == false) {
                alert("请尽量选择相邻座位，请不要留下单个座位!");
                return false;
            }
            if (SelectSeat(Seatobj, true) == false) {
                alert("最多只能预定4个座位");
                return false;
            }
            if ($("#span_Seat").html() == "")
            { $("#span_Seat").html(Seatobj.title); }
            else
            { $("#span_Seat").html($("#span_Seat").html() + "、" + Seatobj.title); }
            Seatobj.bIsOwn = 1;
            Seatobj.src = "/style/img/Seat_own.gif";
            OwnSelectedSeatCount = OwnSelectedSeatCount + 1;
            $("#mCountMoney").val(parseFloat($("#mPrice").val()) * OwnSelectedSeatCount)
            //$("#span_CountMoney").html("&nbsp;&nbsp;&nbsp;合计:" + $("#mCountMoney").val() + "元");
            $("#btn_Bookingseat").attr("disabled", false);
        }
        else {
            SelectSeat(Seatobj, false);
            var st1 = $("#span_Seat").html();
            st1 = st1.replace("、" + Seatobj.title, "");
            st1 = st1.replace(Seatobj.title + "、", "");
            st1 = st1.replace(Seatobj.title, "");
            $("#span_Seat").html(st1);
            Seatobj.bIsOwn = 0;
            Seatobj.src = "/style/img/Seat_null.gif";
            OwnSelectedSeatCount = OwnSelectedSeatCount - 1;
            $("#mCountMoney").val(parseFloat($("#mPrice").val()) * OwnSelectedSeatCount)
           // $("#span_CountMoney").html("&nbsp;&nbsp;&nbsp;合计:" + $("#mCountMoney").val() + "元")
            if (OwnSelectedSeatCount == 0) {
                $("#span_CountMoney").html("");
                $("#btn_Bookingseat").attr("disabled", true);
            }
        }
    }
    function IsAddSeat(SeatO) {
        var l1_SeatO = document.getElementById("imgSeat_" + SeatO.iRowNum + "_" + (parseInt(SeatO.iColNum) - 1))
        var l2_SeatO = document.getElementById("imgSeat_" + SeatO.iRowNum + "_" + (parseInt(SeatO.iColNum) - 2))
        var R1_SeatO = document.getElementById("imgSeat_" + SeatO.iRowNum + "_" + (parseInt(SeatO.iColNum) + 1))
        var R2_SeatO = document.getElementById("imgSeat_" + SeatO.iRowNum + "_" + (parseInt(SeatO.iColNum) + 2))

        if (l1_SeatO != null && R1_SeatO != null) {
            if (l1_SeatO.bIsOwn == 0 && l1_SeatO.bIsSeatLock == 0) {
                if (l2_SeatO == null) {
                    if (R1_SeatO.bIsOwn == 0) {
                        return false;
                    }
                }
                else {
                    if (l2_SeatO.bIsSeatLock == 1) {
                        return false;
                    }
                    if (l2_SeatO != null) {
                        if (l2_SeatO.bIsOwn == 1 && l1_SeatO.bIsOwn == 0) {
                            return false;
                        }
                    }
                    if (R2_SeatO != null) {
                        if (R2_SeatO.bIsOwn == 1 && R1_SeatO.bIsOwn == 0) {
                            return false;
                        }
                    }
                }
            }
        }
        if (R1_SeatO != null && l1_SeatO != null) {
            if (R1_SeatO.bIsOwn == 0 && R1_SeatO.bIsSeatLock == 0) {
                if (R2_SeatO == null) {
                    if (l1_SeatO.bIsOwn == 0) {
                        return false;
                    }
                }
                else {
                    if (R2_SeatO.bIsSeatLock == 1) {
                        return false;
                    }
                    if (l2_SeatO != null) {
                        if (l2_SeatO.bIsOwn == 1 && l1_SeatO.bIsOwn == 0) {
                            return false;
                        }
                    }
                    if (R2_SeatO != null) {
                        if (R2_SeatO.bIsOwn == 1 && R1_SeatO.bIsOwn == 0) {
                            return false;
                        }
                    }
                }
            }
        }
    }
    function SelectSeat(oSeatobj, bIsAdd) {
        if (bIsAdd == true) {
            var badd = false;
            if (OwnSelectedSeatCount == 4) {
                return badd;
            }
            for (var i = 0; i < 5; i++) {
                if (OwnSelectedSeat[i][0] == null) {
                    OwnSelectedSeat[i][0] = oSeatobj.title;
                    OwnSelectedSeat[i][1] = oSeatobj.getAttribute("iRowNum");
                    OwnSelectedSeat[i][2] = oSeatobj.getAttribute("iColNum");
                    OwnSelectedSeat[i][3] = oSeatobj.getAttribute("sRowName");
                    OwnSelectedSeat[i][4] = oSeatobj.getAttribute("sColName");
                    OwnSelectedSeat[i][5] = oSeatobj.getAttribute("Seat_No");
                    badd = true;
                    break;
                }
            }
            return badd
        }
        else {
            for (var i = 0; i < 5; i++) {
                if (OwnSelectedSeat[i][0] == oSeatobj.title) {
                    OwnSelectedSeat[i][0] = null;
                    OwnSelectedSeat[i][1] = null;
                    OwnSelectedSeat[i][2] = null;
                    OwnSelectedSeat[i][3] = null;
                    OwnSelectedSeat[i][4] = null;
                    OwnSelectedSeat[i][5] = null;
                    break;
                }
            }
        }
    }
	var quickiCinemaID=-1;
	var quickiMovieID=-1;
	var cur_iCityID=2;
	function selectQuickCinema(cinema)
    { 
	    quickiCinemaID=cinema.id.toString().split("_")[1];
        var cinema=cinema.innerHTML;
	
        $("#sSelectCinema").val(cinema);
		$("#quickiCinemaID").val(quickiCinemaID);
    }
	function selectQuickMovie(movie)
	{
	   quickiMovieID=movie.id.toString().split("_")[1];
	   var movie=movie.innerHTML;
       $("#sSelectMoives").val(movie);
	   $("#quickMovieID").val(quickiMovieID);
	}
    function selectQuickRoomMovie(roomMovie)
	{
	   quickiRoomMovieID=roomMovie.id.toString().split("_")[1];
	   var mTime=roomMovie.innerHTML;
       $("#sSelectTime").val(mTime);
	   $("#iRoomMovieID").val(quickiRoomMovieID);
	   frmt1.action="index.asp";
	   frmt1.submit();
	}
    //{"SeatInfo":[{"Row":"6","Cell":"05"}]}
    
}