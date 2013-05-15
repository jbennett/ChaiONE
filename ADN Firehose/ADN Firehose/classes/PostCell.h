//
//  PostCell.h
//  ADN Firehose
//
//  Created by Jonathan Bennett on 2013-05-15.
//  Copyright (c) 2013 Jonathan Bennett. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface PostCell : UITableViewCell

@property (nonatomic, weak) IBOutlet UILabel *username;
@property (nonatomic, weak) IBOutlet UILabel *text;
@property (nonatomic, weak) IBOutlet UIImageView *image;

@end
