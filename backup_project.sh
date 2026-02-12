#!/bin/bash

# اسم ملف النسخة
BACKUP_NAME="laravel_project_backup_$(date +%Y%m%d_%H%M%S).zip"

# تحديد الملفات اللي هنستثنيها من الضغط
EXCLUDES=(
  "vendor/*"
  "node_modules/*"
  "storage/logs/*"
  "storage/framework/cache/*"
  "storage/framework/sessions/*"
  "storage/framework/views/*"
  ".git/*"
)

# بناء الأمر النهائي
ZIP_CMD="zip -r $BACKUP_NAME ."

for exclude in "${EXCLUDES[@]}"; do
  ZIP_CMD="$ZIP_CMD -x $exclude"
done

echo "جاري إنشاء نسخة احتياطية: $BACKUP_NAME"
eval $ZIP_CMD
echo "تم إنشاء النسخة بنجاح."
