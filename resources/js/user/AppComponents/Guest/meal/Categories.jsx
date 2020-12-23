import React, { useState } from "react";
import { MdArrowDropDown, MdArrowDropUp } from "react-icons/md";
import SubCategory from "./SubCategory";

const Categories = (props) => {
  const [show, setShow] = useState(false);
  return (
    <div className="mt-3">
      <div
        onClick={() => setShow(!show)}
        className="pl-3 cursor"
        style={{
          fontSize: "18px",
          backgroundColor: "#ff7417",
          width: "100%",
          height: "60px",
          color: "white",
          display: "flex",
          justifyContent: "flex-start",
          alignItems: "center",
        }}
      >
        {props.data.name}{" "}
        {show ? (
          <MdArrowDropUp style={{ color: "#ffffff", fontSize: "25px" }} />
        ) : (
          <MdArrowDropDown style={{ color: "#ffffff", fontSize: "25px" }} />
        )}
      </div>
      {props.data.subcategories.map((data, index) => (
        <div key={index} style={{ display: show ? "block" : "none" }}>
          <SubCategory
            notifySuccess={props.notifySuccess}
            handleAddCart={props.handleAddCart}
            {...props}
            data={data}
          />
        </div>
      ))}
    </div>
  );
};

export default Categories;
