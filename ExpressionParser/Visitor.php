<?php
    interface Visitor {
        function visitBinaryExpr($expr);
        function visitCallExpr($expr);
        function visitGroupingExpr($expr);
        function visitLiteralExpr($expr);
        function visitUnaryExpr($expr);
        function visitVariableExpr($expr);
        function visitIntervalExpr($expr);
    }